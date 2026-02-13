<?php

namespace App\Modules\Delivery\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'origin' => [
                'address' => $this->origin_address,
                'lat' => (float) $this->origin_lat,
                'lng' => (float) $this->origin_lng,
            ],
            'destination' => [
                'address' => $this->destination_address,
                'lat' => (float) $this->destination_lat,
                'lng' => (float) $this->destination_lng,
            ],
            'distance_km' => (float) $this->distance_km,
            'duration_minutes' => (int) $this->duration_minutes,
            'estimated_cost' => (float) $this->estimated_cost,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
        ];
    }
}
