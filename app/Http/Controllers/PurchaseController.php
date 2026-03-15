<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with('supplier')->latest()->paginate(10);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($purchases);
        }

        return view('purchase.index', [
            'purchases' => $purchases,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'payment_method_reference' => 'nullable|string',
            'payment_method_note' => 'nullable|string',
            'payment_method_reference_note' => 'nullable|string',
        ]);

        $supplier = Supplier::find($validated['supplier_id']);
        $commissionPercent = (float) ($supplier?->commission_percent ?? 0);
        $commissionAmount = ((float) $validated['total']) * $commissionPercent / 100;

        $purchase = new Purchase();
        $purchase->forceFill($validated);
        $purchase->discount = $validated['discount'] ?? 0;
        $purchase->paid = $validated['paid'] ?? 0;
        $purchase->balance = $validated['balance'] ?? 0;
        $purchase->payment_method = $validated['payment_method'] ?? 'cash';
        $purchase->payment_status = $validated['payment_status'] ?? 'pending';
        $purchase->payment_reference = $validated['payment_reference'] ?? '';
        $purchase->payment_note = $validated['payment_note'] ?? '';
        $purchase->payment_method_reference = $validated['payment_method_reference'] ?? '';
        $purchase->payment_method_note = $validated['payment_method_note'] ?? '';
        $purchase->payment_method_reference_note = $validated['payment_method_reference_note'] ?? '';
        $purchase->commission_percent = $commissionPercent;
        $purchase->commission_amount = $commissionAmount;
        $purchase->created_by = Auth::id() ?? 0;
        $purchase->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($purchase, 201);
        }

        return redirect()->route('purchase.index');
    }

    public function show($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return response()->json($purchase);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'purchase_date' => 'sometimes|date',
            'total' => 'sometimes|numeric',
            'discount' => 'nullable|numeric',
            'paid' => 'nullable|numeric',
            'balance' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'payment_reference' => 'nullable|string',
            'payment_note' => 'nullable|string',
            'payment_method_reference' => 'nullable|string',
            'payment_method_note' => 'nullable|string',
            'payment_method_reference_note' => 'nullable|string',
        ]);

        $purchase->forceFill($validated);

        $supplierId = $validated['supplier_id'] ?? $purchase->supplier_id;
        $supplier = $supplierId ? Supplier::find($supplierId) : null;
        $commissionPercent = (float) ($supplier?->commission_percent ?? 0);
        $total = (float) ($validated['total'] ?? $purchase->total);
        $purchase->commission_percent = $commissionPercent;
        $purchase->commission_amount = $total * $commissionPercent / 100;
        $purchase->updated_by = Auth::id() ?? 0;
        $purchase->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($purchase);
        }

        return redirect()->route('purchase.index');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Purchase deleted successfully']);
        }

        return redirect()->route('purchase.index');
    }

    public function print($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $purchase]);
    }

    public function pdf($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $purchase]);
    }

    public function excel($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $purchase]);
    }

    public function csv($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $purchase]);
    }
}
