<?php

namespace App\Modules\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Enums\OrderStatus;
use App\Modules\Delivery\Interfaces\NotificationInterface;
use App\Modules\Delivery\Interfaces\PaymentInterface;
use App\Modules\Delivery\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected PaymentInterface $payment,
        protected NotificationInterface $notification
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-Signature');
        $payload = $request->all();

        if (!$signature || !$this->payment->verifyWebhook($payload, $signature)) {
            return response()->json([
                'message' => 'Невалидная подпись',
                'errors' => ['signature' => ['HMAC-SHA256 не прошёл проверку']]
            ], 403);
        }

        if (($payload['status'] ?? '') === 'success') {
            $orderId = $payload['order_id'] ?? null;
            $order = Order::find($orderId);

            if ($order && $order->status === OrderStatus::PENDING->value) {
                $order->update([
                    'status' => OrderStatus::PAID->value,
                    'paid_at' => now(),
                ]);

                $this->notification->send($order->customer_phone, "Order #{$order->id} paid successfully.");
                Log::info("Order #{$order->id} marked as paid via webhook.");
            }
        }

        return response()->json(['message' => 'Processed']);
    }
}
