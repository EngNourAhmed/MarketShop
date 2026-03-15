<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['order', 'payments'])->latest()->paginate(10);
        return response()->json($invoices);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'net_amount' => 'nullable|numeric',
            'balance_amount' => 'nullable|numeric',
            'status' => 'required|string',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $invoice = new Invoice();
        $invoice->forceFill($validated);
        $invoice->invoice_number = 'INV-' . strtoupper(Str::random(10));
        $invoice->created_by = Auth::id() ?? 0;
        $invoice->save();

        return response()->json($invoice, 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
        return response()->json($invoice);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|exists:orders,id',
            'invoice_date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'total_amount' => 'sometimes|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'tax_amount' => 'nullable|numeric',
            'net_amount' => 'nullable|numeric',
            'balance_amount' => 'nullable|numeric',
            'status' => 'sometimes|string',
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $invoice->forceFill($validated);
        $invoice->updated_by = Auth::id() ?? 0;
        $invoice->save();

        return response()->json($invoice);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function print($id)
    {
        $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $invoice]);
    }

    public function pdf($id)
    {
        $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $invoice]);
    }

    public function excel($id)
    {
        $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $invoice]);
    }

    public function csv($id)
    {
        $invoice = Invoice::with(['order', 'payments'])->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $invoice]);
    }
}
