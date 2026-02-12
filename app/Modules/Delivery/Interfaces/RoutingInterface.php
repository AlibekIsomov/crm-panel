<?php

namespace App\Modules\Delivery\Interfaces;

use App\Modules\Delivery\DTOs\GeoPoint;
use App\Modules\Delivery\DTOs\RouteResult;

interface RoutingInterface
{
    public function calculateRoute(GeoPoint $from, GeoPoint $to): RouteResult;
}
