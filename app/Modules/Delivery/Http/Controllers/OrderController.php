<?php

namespace App\Modules\Delivery\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Enums\OrderStatus;
use App\Modules\Delivery\Http\Requests\CreateOrderRequest;
use App\Modules\Delivery\Http\Resources\OrderResource;
use App\Modules\Delivery\Interfaces\GeocoderInterface;
use App\Modules\Delivery\Interfaces\NotificationInterface;
use App\Modules\Delivery\Interfaces\PaymentInterface;
use App\Modules\Delivery\Interfaces\RoutingInterface;
use App\Modules\Delivery\Jobs\SendNotificationJob;
use App\Modules\Delivery\Models\Order;
use App\Modules\Delivery\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        protected GeocoderInterface $geocoder,
        protected RoutingInterface $routing,
        protected PricingService $pricing,
        protected PaymentInterface $payment,
        protected NotificationInterface $notification
    ) {
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $origin = $this->geocoder->geocode($data['origin_address']);
        $destination = $this->geocoder->geocode($data['destination_address']);

        $route = $this->routing->calculateRoute($origin, $destination);
        $cost = $this->pricing->calculate($route->distanceKm);

        $order = Order::create([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'customer_email' => $data['customer_email'],
            'origin_address' => $data['origin_address'],
            'origin_lat' => $origin->lat,
            'origin_lng' => $origin->lng,
            'destination_address' => $data['destination_address'],
            'destination_lat' => $destination->lat,
            'destination_lng' => $destination->lng,
            'distance_km' => $route->distanceKm,
            'duration_minutes' => $route->durationMinutes,
            'estimated_cost' => $cost,
            'status' => OrderStatus::PENDING->value,
        ]);

        SendNotificationJob::dispatch($order->customer_phone, "Order #{$order->id} created. Status: Pending.");

        return response()->json([
            'data' => new OrderResource($order),
            'message' => 'Заказ создан, SMS отправлено'
        ], 201);
    }

    public function show($id): OrderResource
    {
        $order = Order::findOrFail($id);
        return new OrderResource($order);
    }

    public function calculate(CreateOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $origin = $this->geocoder->geocode($data['origin_address']);
        $destination = $this->geocoder->geocode($data['destination_address']);

        $route = $this->routing->calculateRoute($origin, $destination);
        $cost = $this->pricing->calculate($route->distanceKm);

        return response()->json([
            'data' => [
                'distance_km' => $route->distanceKm,
                'duration_minutes' => $route->durationMinutes,
                'estimated_cost' => $cost,
                'origin' => $origin,
                'destination' => $destination
            ]
        ]);
    }

    public function pay($id): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== OrderStatus::PENDING->value) {
            return response()->json(['message' => 'Order is not pending payment'], 422);
        }

        $result = $this->payment->createPayment($order);

        return response()->json([
            'payment_url' => $result->paymentUrl,
            'transaction_id' => $result->transactionId
        ]);
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $request->validate(['status' => 'required|string']);

        $order = Order::findOrFail($id);
        $newStatus = OrderStatus::tryFrom($request->status);
        $currentStatus = OrderStatus::tryFrom($order->status);

        if (!$newStatus) {
            return response()->json(['message' => 'Invalid status'], 422);
        }

        if (!$currentStatus->canTransitionTo($newStatus)) {
            return response()->json([
                'message' => 'Недопустимый переход',
                'errors' => ['status' => ["Переход из {$currentStatus->value} в {$newStatus->value} невозможен"]]
            ], 422);
        }

        $order->update(['status' => $newStatus->value]);

        SendNotificationJob::dispatch($order->customer_phone, "Order #{$order->id} status updated to: {$newStatus->label()}");

        if ($newStatus === OrderStatus::DELIVERED) {
            SendNotificationJob::dispatch($order->customer_email, "Order #{$order->id} has been delivered.");
        }

        return response()->json(['message' => 'Status updated', 'data' => new OrderResource($order)]);
    }

    public function geocode(Request $request): JsonResponse
    {
        $request->validate(['address' => 'required|string']);
        $address = $request->input('address');
        $geoPoint = $this->geocoder->geocode($address);

        return response()->json([
            'address' => $address,
            'lat' => $geoPoint->lat,
            'lng' => $geoPoint->lng
        ]);
    }
}
