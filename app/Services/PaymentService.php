<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymentService
{
    private $baseUrl;
    private $merchantId;
    private $apiKey;
    private $secret;

    public function __construct()
    {
        $this->baseUrl = config('nttdata.base_url');
        $this->merchantId = config('nttdata.merchant_id');
        $this->apiKey = config('nttdata.api_key');
        $this->secret = config('nttdata.secret');
    }

    public function initiatePayment(array $data)
    {
        $response = Http::withHeaders([
            'API-Key' => $this->apiKey,
            'Merchant-ID' => $this->merchantId,
        ])->post("{$this->baseUrl}/payment/initiate", [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'order_id' => $data['order_id'],
            'callback_url' => $data['callback_url'],
            'customer_details' => $data['customer_details'],
        ]);

        return $response->json();
    }

    public function verifyPayment($transactionId)
    {
        $response = Http::withHeaders([
            'API-Key' => $this->apiKey,
            'Merchant-ID' => $this->merchantId,
        ])->get("{$this->baseUrl}/payment/verify/{$transactionId}");

        return $response->json();
    }
}
