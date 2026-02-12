<?php

namespace App\Modules\Delivery\DTOs;

class PaymentResult
{
    public function __construct(
        public string $paymentUrl,
        public string $transactionId
    ) {
    }
}
