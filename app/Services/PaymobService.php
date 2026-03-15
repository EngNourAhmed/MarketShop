<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected $baseUrl = 'https://accept.paymob.com/api';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
    }

    /**
     * Step 1: Authentication Request
     */
    public function authenticate()
    {
        try {
            $response = Http::post("{$this->baseUrl}/auth/tokens", [
                'api_key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                return $response->json()['token'];
            }

            Log::error('Paymob Auth Failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Auth Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Step 2: Order Registration
     */
    public function registerOrder($token, $amount, $currency = 'EGP', $merchantOrderId = null)
    {
        try {
            $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
                'auth_token' => $token,
                'delivery_needed' => 'false',
                'amount_cents' => $amount * 100,
                'currency' => $currency,
                'merchant_order_id' => $merchantOrderId,
                'items' => [],
            ]);

            if ($response->successful()) {
                return $response->json()['id'];
            }

            Log::error('Paymob Order Registration Failed', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Order Registration Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Step 3: Payment Key Generation
     */
    public function generatePaymentKey($token, $orderId, $amount, $billingData, $integrationId)
    {
        try {
            $billingDataArray = [
                'first_name' => $billingData['first_name'] ?? 'NA',
                'last_name' => $billingData['last_name'] ?? 'NA',
                'email' => $billingData['email'] ?? 'NA',
                'phone_number' => $billingData['phone'] ?? 'NA',
                'apartment' => 'NA',
                'floor' => 'NA',
                'street' => (isset($billingData['address']) && !empty($billingData['address']) && $billingData['address'] !== 'NA') ? $billingData['address'] : 'Cairo Street',
                'building' => 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'Cairo',
                'country' => 'EG',
                'state' => 'NA',
            ];

            $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
                'auth_token' => $token,
                'amount_cents' => $amount * 100,
                'expiration' => 3600,
                'order_id' => $orderId,
                'billing_data' => $billingDataArray,
                'currency' => 'EGP',
                'integration_id' => $integrationId,
                'redirection_url' => route('paymob.response'),
            ]);

            if ($response->successful()) {
                return $response->json()['token'];
            }

            Log::error('Paymob Payment Key Generation Failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'payload' => [
                    'billing_data' => $billingDataArray,
                    'integration_id' => $integrationId
                ]
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob Payment Key Generation Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
