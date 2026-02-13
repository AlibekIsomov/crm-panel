<?php

namespace Tests\Feature\Modules\Delivery;

use App\Modules\Delivery\Enums\OrderStatus;
use App\Modules\Delivery\Models\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_can_create_order()
    {
        $response = $this->postJson('/api/orders', [
            'customer_name' => 'John Doe',
            'customer_phone' => '+1234567890',
            'customer_email' => 'john@example.com',
            'origin_address' => 'Tashkent, Origin St 1',
            'destination_address' => 'Tashkent, Dest St 2',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'status', 'estimated_cost']]);

        $this->assertDatabaseHas('orders', [
            'customer_email' => 'john@example.com',
            'status' => OrderStatus::PENDING->value,
        ]);

        // Check logs exist (geocoder, routing, sms)
        $this->assertDatabaseHas('order_logs', ['service' => 'geocoder']);
        $this->assertDatabaseHas('order_logs', ['service' => 'routing']);
        $this->assertDatabaseHas('order_logs', ['service' => 'sms']);
    }

    public function test_can_calculate_order_cost()
    {
        $response = $this->postJson('/api/orders/calculate', [
            'customer_name' => 'John Doe',
            'customer_phone' => '+1234567890',
            'customer_email' => 'john@example.com',
            'origin_address' => 'A',
            'destination_address' => 'B',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['estimated_cost', 'distance_km']]);
    }

    public function test_can_geocode_address()
    {
        $response = $this->postJson('/api/addresses/geocode', [
            'address' => 'Some address',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['lat', 'lng', 'address']);
    }

    public function test_can_initiate_payment()
    {
        $order = Order::factory()->create(['status' => OrderStatus::PENDING->value]);

        $response = $this->postJson("/api/orders/{$order->id}/pay");

        $response->assertStatus(200)
            ->assertJsonStructure(['payment_url', 'transaction_id']);
    }

    public function test_can_update_status()
    {
        $order = Order::factory()->create(['status' => OrderStatus::PENDING->value]);

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatus::PAID->value,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(OrderStatus::PAID->value, $order->fresh()->status);
    }

    public function test_cannot_transition_to_invalid_status()
    {
        $order = Order::factory()->create(['status' => OrderStatus::CANCELLED->value]);

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(422);
    }
}
