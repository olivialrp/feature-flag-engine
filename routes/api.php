<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FeatureFlagController as AdminFeatureFlagController;
use App\Http\Controllers\Api\V1\FeatureFlagController as SdkFeatureFlagController;
use App\Http\Middleware\EnsureActiveTenant;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/flags', [SdkFeatureFlagController::class, 'index']);
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', EnsureActiveTenant::class])->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('feature-flags', AdminFeatureFlagController::class);
});
