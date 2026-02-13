<?php

namespace Tests\Unit\Modules\Delivery;

use App\Modules\Delivery\Services\PricingService;
use PHPUnit\Framework\TestCase;

class PricingServiceTest extends TestCase
{
    public function test_calculates_base_cost_correctly()
    {
        $service = new PricingService();
        // 10 km: 5000 + 10 * 800 = 13000
        $this->assertEquals(13000, $service->calculate(10));
    }

    public function test_calculates_long_distance_with_multiplier()
    {
        $service = new PricingService();
        // 200 km: (5000 + 200 * 800) * 1.5 = (5000 + 160000) * 1.5 = 165000 * 1.5 = 247500
        $this->assertEquals(247500, $service->calculate(200));
    }
}
