<?php

namespace App\Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'origin_lat' => 'float',
        'origin_lng' => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
        'distance_km' => 'float',
        'estimated_cost' => 'float',
        'paid_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];
    protected static function newFactory()
    {
        return \App\Modules\Delivery\Database\Factories\OrderFactory::new();
    }
}
