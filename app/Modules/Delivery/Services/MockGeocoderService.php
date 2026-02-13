<?php

namespace App\Modules\Delivery\Services;

use App\Modules\Delivery\DTOs\GeoPoint;
use App\Modules\Delivery\Interfaces\GeocoderInterface;
use App\Modules\Delivery\Models\OrderLog;

class MockGeocoderService implements GeocoderInterface
{
    public function geocode(string $address): GeoPoint
    {
        $startTime = microtime(true);

        $lat = rand(4128000, 4135000) / 100000;
        $lng = rand(6920000, 6935000) / 100000;

        $duration = (microtime(true) - $startTime) * 1000;

        OrderLog::create([
            'service' => 'geocoder',
            'method' => 'geocode',
            'url' => 'mock://geocoder',
            'request_body' => json_encode(['address' => $address]),
            'response_body' => json_encode(['lat' => $lat, 'lng' => $lng]),
            'status_code' => 200,
            'duration_ms' => $duration
        ]);

        return new GeoPoint($lat, $lng);
    }
}
