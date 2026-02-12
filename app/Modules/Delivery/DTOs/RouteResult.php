<?php

namespace App\Modules\Delivery\DTOs;

class RouteResult
{
    public function __construct(
        public float $distanceKm,
        public int $durationMinutes
    ) {
    }
}
