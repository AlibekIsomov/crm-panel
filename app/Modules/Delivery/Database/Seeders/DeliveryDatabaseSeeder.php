<?php

namespace App\Modules\Delivery\Database\Seeders;

use App\Modules\Delivery\Models\Order;
use Illuminate\Database\Seeder;

class DeliveryDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 5 random pending orders
        Order::factory()->count(5)->create([
            'status' => 'pending',
            'paid_at' => null,
        ]);

        // 5 random paid orders
        Order::factory()->count(5)->create([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // 5 delivered orders
        Order::factory()->count(5)->create([
            'status' => 'delivered',
            'paid_at' => now()->subDay(),
        ]);
    }
}
