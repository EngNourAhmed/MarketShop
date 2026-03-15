<?php

namespace App\Http\Controllers;

use App\Http\Resources\DebtResponse;
use Illuminate\Http\Request;
use App\Models\Debt;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\SupplierWithdrawRequest;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $debts = Debt::with(['supplier', 'customer'])->get();
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(DebtResponse::collection($debts));
        }

        $suppliers = Supplier::query()->orderBy('name')->get();
        $customers = Customer::query()->orderBy('name')->get();

        $vendorTotal = (float) $debts->where('type', 'vendor')->sum('amount');
        $factoryTotal = (float) $debts->where('type', 'factory')->sum('amount');
        $totalDebts = (float) $debts->sum('amount');

        $ordersGrossBySupplierId = Order::query()
            ->where('status', 'delivered')
            ->whereDoesntHave('returns', function ($q) {
                $q->where('status', 'approved');
            })
            ->selectRaw('supplier_id, SUM(COALESCE(total, 0)) as gross_total')
            ->groupBy('supplier_id')
            ->pluck('gross_total', 'supplier_id')
            ->map(fn ($v) => (float) $v)
            ->all();

        $withdrawApprovedBySupplierId = SupplierWithdrawRequest::query()
            ->where('status', 'approved')
            ->selectRaw('supplier_id, SUM(COALESCE(approved_amount, amount, 0)) as approved_total')
            ->groupBy('supplier_id')
            ->pluck('approved_total', 'supplier_id')
            ->map(fn ($v) => (float) $v)
            ->all();

        $vendorAvailableTotal = 0.0;
        $factoryAvailableTotal = 0.0;
        $availableTotal = 0.0;

        foreach ($suppliers as $s) {
            $sid = (int) ($s->id ?? 0);
            if ($sid <= 0) {
                continue;
            }

            $gross = (float) ($ordersGrossBySupplierId[$sid] ?? 0);
            $commissionPercent = (float) ($s->commission_percent ?? 0);
            if ($commissionPercent < 0) {
                $commissionPercent = 0;
            }
            if ($commissionPercent > 100) {
                $commissionPercent = 100;
            }

            $net = $gross - ($gross * $commissionPercent / 100.0);
            $withdrawApproved = (float) ($withdrawApprovedBySupplierId[$sid] ?? 0);

            $available = $net - $withdrawApproved;
            if ($available < 0) {
                $available = 0.0;
            }

            $availableTotal += $available;
            if (($s->type ?? '') === 'vendor') {
                $vendorAvailableTotal += $available;
            }
            if (($s->type ?? '') === 'factory') {
                $factoryAvailableTotal += $available;
            }
        }

        $vendorTotal += $vendorAvailableTotal;
        $factoryTotal += $factoryAvailableTotal;
        $totalDebts += $availableTotal;

        return view('debts.index', [
            'debts' => $debts,
            'vendorTotal' => $vendorTotal,
            'factoryTotal' => $factoryTotal,
            'totalDebts' => $totalDebts,
            'suppliers' => $suppliers,
            'customers' => $customers,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'customer_id' => 'nullable|exists:customers,id',
            'description' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'amount' => 'required|numeric',
            'due_date' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $validated['type'] = (string) ($supplier->type ?? '');
        $commissionPercent = (float) ($supplier?->commission_percent ?? 0);
        $commissionAmount = ((float) $validated['amount']) * $commissionPercent / 100;

        $debt = new Debt();
        $debt->forceFill($validated);
        $debt->commission_percent = $commissionPercent;
        $debt->commission_amount = $commissionAmount;
        $debt->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($debt, 201);
        }

        return redirect()->route('debts.index')->with('status', 'تم إضافة المديونية بنجاح');
    }

    public function show($id)
    {
        $debt = Debt::with(['supplier', 'customer'])->findOrFail($id);
        return response()->json($debt);
    }

    public function update(Request $request, $id)
    {
        $debt = Debt::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'customer_id' => 'sometimes|nullable|exists:customers,id',
            'description' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|nullable|string|max:255',
            'amount' => 'sometimes|required|numeric',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|string|max:255',
        ]);

        $supplierId = $validated['supplier_id'] ?? $debt->supplier_id;
        $amount = (float) ($validated['amount'] ?? $debt->amount);
        $supplier = $supplierId ? Supplier::find($supplierId) : null;

        if ($supplier) {
            $validated['type'] = (string) ($supplier->type ?? '');
        }

        $debt->forceFill($validated);
        $commissionPercent = (float) ($supplier?->commission_percent ?? 0);
        $debt->commission_percent = $commissionPercent;
        $debt->commission_amount = $amount * $commissionPercent / 100;
        $debt->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($debt);
        }

        return redirect()->route('debts.index')->with('status', 'تم تعديل المديونية بنجاح');
    }

    public function destroy($id)
    {
        $debt = Debt::findOrFail($id);
        $debt->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Debt deleted successfully']);
        }

        return redirect()->route('debts.index')->with('status', 'تم حذف المديونية بنجاح');
    }
}
