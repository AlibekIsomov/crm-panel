<?php

use App\Modules\Delivery\Http\Controllers\OrderController;
use App\Modules\Delivery\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Orders
    Route::post('/orders/calculate', [OrderController::class, 'calculate']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'pay']);

    // Webhooks
    Route::post('/webhooks/payment', [WebhookController::class, 'handle']);

    // Addresses
    Route::post('/addresses/geocode', [OrderController::class, 'geocode']);
});
