<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('tasks', TaskController::class);
    Route::get('/tasks/today', [TaskController::class, 'today']);
    Route::get('/tasks/overdue', [TaskController::class, 'overdue']);
    Route::get('/clients/{client}/tasks', [TaskController::class, 'getClientTasks']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus']);
});
