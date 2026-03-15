<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('invoice')->latest()->paginate(10);
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        $payment = new Payment();
        $payment->forceFill($validated);
        
        // Fill required fields with defaults if not provided
        $payment->payment_reference = $request->payment_reference ?? Str::random(10);
        $payment->payment_url = $request->payment_url ?? '';
        $payment->payment_token = $request->payment_token ?? '';
        $payment->payment_signature = $request->payment_signature ?? '';
        $payment->payment_ip = $request->ip();
        $payment->payment_ip_address = $request->ip();
        $payment->payment_ip_country = '';
        $payment->payment_ip_city = '';
        $payment->payment_ip_state = '';
        $payment->payment_ip_zip = '';

        $payment->save();

        return response()->json($payment, 201);
    }

    public function show($id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);
        return response()->json($payment);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'invoice_id' => 'sometimes|exists:invoices,id',
            'amount' => 'sometimes|numeric',
            'payment_method' => 'sometimes|string',
            'payment_status' => 'sometimes|string',
            'payment_reference' => 'nullable|string',
        ]);

        $payment->forceFill($validated);
        $payment->save();

        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

    public function print($id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $payment]);
    }

    public function pdf($id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $payment]);
    }

    public function excel($id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $payment]);
    }

    public function csv($id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $payment]);
    }
}
