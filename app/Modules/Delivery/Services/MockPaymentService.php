<?php

namespace App\Modules\Delivery\Services;

use App\Modules\Delivery\DTOs\PaymentResult;
use App\Modules\Delivery\Interfaces\PaymentInterface;
use App\Modules\Delivery\Models\Order;
use App\Modules\Delivery\Models\OrderLog;
use Illuminate\Support\Str;

class MockPaymentService implements PaymentInterface
{
    public function createPayment(Order $order): PaymentResult
    {
        $startTime = microtime(true);
        $txnId = 'txn_' . Str::random(10);
        $url = "https://pay.mock/checkout/{$txnId}?amount={$order->estimated_cost}";

        OrderLog::create([
            'service' => 'payment',
            'method' => 'createPayment',
            'url' => 'mock://payment/create',
            'request_body' => json_encode(['order_id' => $order->id, 'amount' => $order->estimated_cost]),
            'response_body' => json_encode(['url' => $url, 'txn_id' => $txnId]),
            'status_code' => 200,
            'duration_ms' => (microtime(true) - $startTime) * 1000
        ]);

        return new PaymentResult($url, $txnId);
    }

    public function verifyWebhook(array $payload, string $signature): bool
    {
        $secret = config('services.payment.secret', 'default_secret');
        // JSON encode payload to match how it would likely be sent
        // ideally we should get raw body, but for array input we re-encode
        // For simplicity/robustness in this mock, we assume payload is the data array
        // and we check against the encoded version or just the data if signature algorithm is known.
        // Standard HMAC usually works on raw body. 
        // In WebhookController we should probably pass raw body or ensure consistent encoding.
        // Here we'll assume the controller passes the raw body or equivalent.
        // Wait, the interface says `array $payload`.
        // Let's assume the signature was generated from `json_encode($payload)`.

        $computed = hash_hmac('sha256', json_encode($payload), $secret);

        return hash_equals($computed, $signature);
    }
}
