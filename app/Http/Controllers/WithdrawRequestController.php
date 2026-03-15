<?php

namespace App\Http\Controllers;

use App\Http\Resources\WithdrawResponse;
use Illuminate\Http\Request;
use App\Models\WithdrawRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class WithdrawRequestController extends Controller
{
    public function index(Request $request)
    {
        $withdrawRequests = WithdrawRequest::with(['user', 'customer'])->latest()->get();
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(WithdrawResponse::collection($withdrawRequests));
        }

        return view('withdraw.index', [
            'withdrawRequests' => $withdrawRequests,
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name', 'phone']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'currency' => 'required|string',
            'points' => 'required|integer',
            'description' => 'nullable|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'reference' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'bank_city' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $validated['description'] = $validated['description'] ?? '';
        $validated['reference'] = $validated['reference'] ?? '';
        $validated['bank_name'] = $validated['bank_name'] ?? '';
        $validated['account_number'] = $validated['account_number'] ?? '';
        $validated['account_name'] = $validated['account_name'] ?? '';
        $validated['bank_address'] = $validated['bank_address'] ?? '';
        $validated['bank_city'] = $validated['bank_city'] ?? '';
        $validated['status'] = $validated['status'] ?? 'pending';

        $validated['approved_by'] = 0;
        $validated['approved_at'] = '';
        $validated['rejected_by'] = 0;
        $validated['rejected_at'] = '';

        $withdrawRequest = new WithdrawRequest();
        $withdrawRequest->forceFill($validated);
        $withdrawRequest->user_id = Auth::id();
        $withdrawRequest->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($withdrawRequest, 201);
        }

        return redirect()->route('withdrawRequest.index')->with('status', 'تمت الإضافة بنجاح');
    }

    public function show($id)
    {
        $withdrawRequest = WithdrawRequest::with(['user', 'customer'])->findOrFail($id);
        return response()->json($withdrawRequest);
    }

    public function update(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'currency' => 'sometimes|string',
            'points' => 'sometimes|integer',
            'description' => 'nullable|string',
            'payment_method' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'reference' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'bank_address' => 'nullable|string',
            'bank_city' => 'nullable|string',
            'status' => 'sometimes|string',
        ]);

        if (array_key_exists('description', $validated) && $validated['description'] === null) {
            $validated['description'] = '';
        }
        if (array_key_exists('reference', $validated) && $validated['reference'] === null) {
            $validated['reference'] = '';
        }
        foreach (['bank_name', 'account_number', 'account_name', 'bank_address', 'bank_city'] as $k) {
            if (array_key_exists($k, $validated) && $validated[$k] === null) {
                $validated[$k] = '';
            }
        }

        if (array_key_exists('status', $validated)) {
            if ($validated['status'] === 'approved') {
                $validated['approved_by'] = Auth::id() ?? 0;
                $validated['approved_at'] = now()->toDateTimeString();
            }

            if ($validated['status'] === 'rejected') {
                $validated['rejected_by'] = Auth::id() ?? 0;
                $validated['rejected_at'] = now()->toDateTimeString();
            }
        }

        $withdrawRequest->forceFill($validated);
        $withdrawRequest->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($withdrawRequest);
        }

        return redirect()->route('withdrawRequest.index')->with('status', 'تم التعديل بنجاح');
    }

    public function destroy(Request $request, $id)
    {
        $withdrawRequest = WithdrawRequest::findOrFail($id);
        $withdrawRequest->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Withdraw request deleted successfully']);
        }

        return redirect()->route('withdrawRequest.index')->with('status', 'تم الحذف بنجاح');
    }
}
