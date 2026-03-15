<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commission;
use App\Http\Resources\CommissionResponse;
use App\Models\Sale;
use App\Models\User;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $commissionsQuery = Commission::with(['user', 'sale.customer', 'order.returns', 'supplier']);

        $from = $request->query('from');
        $to = $request->query('to');

        if (!empty($from) && !empty($to)) {
            $commissionsQuery->whereBetween('date', [$from, $to]);
        } elseif (!empty($from)) {
            $commissionsQuery->where('date', '>=', $from);
        } elseif (!empty($to)) {
            $commissionsQuery->where('date', '<=', $to);
        }

        $commissions = $commissionsQuery->latest('date')->get();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(CommissionResponse::collection($commissions));
        }

        return view('commission.index', [
            'commissions' => $commissions,
            'sales' => Sale::with('customer')->latest()->get(['id', 'invoice_number', 'customer_id', 'invoice_date', 'grand_total', 'total']),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'sale_id' => 'nullable|exists:sales,id',
            'order_id' => 'nullable|exists:orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'commission_type' => 'nullable|string',
            'commission_type_id' => 'nullable|integer',
            'commission' => 'required|numeric',
            'order_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'date' => 'required|date',
            'status' => 'required|string',
        ]);

        if (empty($validated['sale_id']) && empty($validated['order_id'])) {
            return back()->withErrors([
                'sale_id' => 'Sale or Order is required.',
            ])->withInput();
        }

        if (empty($validated['commission_type'])) {
            $validated['commission_type'] = 'vendor';
        }

        $commission = new Commission();
        $commission->forceFill($validated);
        $commission->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($commission, 201);
        }

        return redirect()->route('commission.index')->with('status', 'تمت الإضافة بنجاح');
    }

    public function show($id)
    {
        $commission = Commission::with(['user', 'sale'])->findOrFail($id);
        return response()->json($commission);
    }

    public function update(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'sale_id' => 'nullable|exists:sales,id',
            'order_id' => 'nullable|exists:orders,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'commission_type' => 'nullable|string',
            'commission_type_id' => 'nullable|integer',
            'commission' => 'sometimes|numeric',
            'order_amount' => 'sometimes|numeric',
            'tax_amount' => 'sometimes|numeric',
            'date' => 'sometimes|date',
            'status' => 'sometimes|string',
        ]);

        $commission->forceFill($validated);
        $commission->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($commission);
        }

        return redirect()->route('commission.index')->with('status', 'تم التعديل بنجاح');
    }

    public function destroy(Request $request, $id)
    {
        $commission = Commission::findOrFail($id);
        $commission->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Commission deleted successfully']);
        }

        return redirect()->route('commission.index')->with('status', 'تم الحذف بنجاح');
    }
}
