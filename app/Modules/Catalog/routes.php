<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Catalog\Http\Controllers\CategoryController;
use App\Modules\Catalog\Http\Controllers\ProductController;

Route::prefix('api/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
});

Route::prefix('api/products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{slug}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
});
