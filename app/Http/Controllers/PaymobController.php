<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\PaymobService;

class PaymobController extends Controller
{
    protected $paymobService;
    protected $orderId;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    /**
     * Step 1: Get Auth Token
     * Step 2: Create Order
     * Step 3: Get Payment Key
     * Step 4: Redirect to Iframe
     */
    public function payWithCard($orderId)
    {
        try {
            $order = \App\Models\Order::with('customer')->findOrFail($orderId);
            $user = \Illuminate\Support\Facades\Auth::user();

            // 1. Authentication Request
            $authToken = $this->paymobService->authenticate();

            if (!$authToken) {
                Log::error('Paymob Auth Failed: Check your PAYMOB_API_KEY in .env');
                return back()->with('error', 'Authentication failed with payment gateway. Please contact support.');
            }

            // 2. Order Registration
            $paymobOrderId = $this->paymobService->registerOrder(
                $authToken,
                $order->total,
                'EGP',
                $order->order_code
            );

            if (!$paymobOrderId) {
                return back()->with('error', 'Failed to register order with payment gateway.');
            }

            // 3. Payment Key Generation
            $billingData = [
                'first_name' => $user->name ?? $order->customer->name ?? 'NA',
                'last_name'  => 'NA',
                'email'      => $user->email ?? $order->customer->email ?? 'NA',
                'phone'      => $user->phone ?? $order->customer->phone ?? 'NA',
                'address'    => $user->address ?? $order->customer->address ?? 'NA',
            ];

            $paymentToken = $this->paymobService->generatePaymentKey(
                $authToken,
                $paymobOrderId,
                $order->total,
                $billingData,
                config('services.paymob.integration_id')
            );

            if (!$paymentToken) {
                return back()->with('error', 'Failed to generate payment token.');
            }

            $iframeId = config('services.paymob.iframe_id', '1010691');
            $iframeUrl = "https://accept.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentToken}";

            if (strtolower(trim($order->payment_method)) === 'card') {
                return redirect()->away($iframeUrl);
            }

            // Return Embedded Custom View (Iframe) for other methods or fallback
            return view('shop.checkout.paymob_iframe', compact('iframeUrl', 'order', 'paymentToken'));
        } catch (\Exception $e) {
            Log::error('Paymob Integration Exception', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function response(Request $request)
    {
        $data = $request->all();

        // Success condition: success=true and txn_response_code=0
        $success = ($data['success'] ?? '') === 'true';
        $orderCode = $data['merchant_order_id'] ?? null;

        if ($success) {
            $order = \App\Models\Order::where('order_code', $orderCode)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
            }
            return redirect()->route('shop.orders.index')->with('success', 'Payment successful!');
        }

        return redirect()->route('shop.orders.index')->with('error', 'Payment failed or cancelled.');
    }

    public function webhook(Request $request)
    {
        Log::info('Paymob Webhook Received', $request->all());

        $data = $request->input('obj');
        if ($data && ($data['success'] ?? false) == true) {
            $orderCode = $data['order']['merchant_order_id'] ?? null;
            $order = \App\Models\Order::where('order_code', $orderCode)->first();
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
