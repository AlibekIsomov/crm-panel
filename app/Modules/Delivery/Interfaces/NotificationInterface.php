<?php

namespace App\Modules\Delivery\Interfaces;

interface NotificationInterface
{
    public function send(string $recipient, string $message): bool;
}
