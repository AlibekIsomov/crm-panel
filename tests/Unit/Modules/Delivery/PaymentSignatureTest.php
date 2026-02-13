<?php

namespace Tests\Unit\Modules\Delivery;

use App\Modules\Delivery\Services\MockPaymentService;
use Tests\TestCase;

class PaymentSignatureTest extends TestCase
{
    public function test_validates_correct_signature()
    {
        $service = new MockPaymentService();
        $payload = ['order_id' => 1, 'amount' => 1000];
        $secret = config('services.payment.secret', 'default_secret');
        ksort($payload);
        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        $this->assertTrue($service->verifyWebhook($payload, $signature));
    }

    public function test_rejects_incorrect_signature()
    {
        $service = new MockPaymentService();
        $payload = ['order_id' => 1, 'amount' => 1000];
        $signature = 'invalid_signature';

        $this->assertFalse($service->verifyWebhook($payload, $signature));
    }
}
