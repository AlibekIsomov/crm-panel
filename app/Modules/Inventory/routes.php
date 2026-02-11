<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Http\Controllers\InventoryController;

Route::prefix('api/inventory')->group(function () {
    Route::post('/{product_id}/adjust', [InventoryController::class, 'adjust']);
    Route::get('/{product_id}/history', [InventoryController::class, 'history']);
});
