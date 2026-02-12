<?php

namespace App\Modules\Delivery\Services;

use App\Modules\Delivery\DTOs\GeoPoint;
use App\Modules\Delivery\DTOs\RouteResult;
use App\Modules\Delivery\Interfaces\RoutingInterface;
use App\Modules\Delivery\Models\OrderLog;

class MockRoutingService implements RoutingInterface
{
    public function calculateRoute(GeoPoint $from, GeoPoint $to): RouteResult
    {
        $startTime = microtime(true);

        // Haversine implementation
        $earthRadius = 6371; // km

        $latFrom = deg2rad($from->lat);
        $lonFrom = deg2rad($from->lng);
        $latTo = deg2rad($to->lat);
        $lonTo = deg2rad($to->lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        $distance = $earthRadius * $angle * 1.3; // Mock factor 1.3
        $duration = ($distance / 40) * 60; // 40 km/h avg speed

        $durationMs = (microtime(true) - $startTime) * 1000;

        OrderLog::create([
            'service' => 'routing',
            'method' => 'calculateRoute',
            'url' => 'mock://routing',
            'request_body' => json_encode(['from' => $from, 'to' => $to]),
            'response_body' => json_encode(['distance' => $distance, 'duration' => $duration]),
            'status_code' => 200,
            'duration_ms' => $durationMs
        ]);

        return new RouteResult(round($distance, 2), (int) round($duration));
    }
}
