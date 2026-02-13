<?php

namespace App\Modules\Delivery\Jobs;

use App\Modules\Delivery\Interfaces\NotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $recipient,
        protected string $message
    ) {
    }

    public function handle(NotificationInterface $notification): void
    {
        $notification->send($this->recipient, $this->message);
    }
}
