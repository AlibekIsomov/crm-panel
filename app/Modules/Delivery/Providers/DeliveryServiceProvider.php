<?php

namespace App\Modules\Delivery\Providers;

use App\Modules\Delivery\Interfaces\GeocoderInterface;
use App\Modules\Delivery\Interfaces\NotificationInterface;
use App\Modules\Delivery\Interfaces\PaymentInterface;
use App\Modules\Delivery\Interfaces\RoutingInterface;
use App\Modules\Delivery\Services\MockGeocoderService;
use App\Modules\Delivery\Services\MockNotificationService;
use App\Modules\Delivery\Services\MockPaymentService;
use App\Modules\Delivery\Services\MockRoutingService;
use Illuminate\Support\ServiceProvider;

class DeliveryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GeocoderInterface::class, MockGeocoderService::class);
        $this->app->bind(RoutingInterface::class, MockRoutingService::class);
        $this->app->bind(PaymentInterface::class, MockPaymentService::class);
        $this->app->bind(NotificationInterface::class, MockNotificationService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
