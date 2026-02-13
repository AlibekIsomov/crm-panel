<?php

namespace App\Modules\Delivery\Database\Factories;

use App\Modules\Delivery\Enums\OrderStatus;
use App\Modules\Delivery\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->phoneNumber,
            'customer_email' => $this->faker->safeEmail,
            'origin_address' => $this->faker->address,
            'destination_address' => $this->faker->address,
            'status' => OrderStatus::PENDING->value,
            'distance_km' => $this->faker->randomFloat(2, 1, 50),
            'estimated_cost' => $this->faker->randomFloat(2, 5000, 50000),
        ];
    }
}
