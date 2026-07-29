<?php

use App\Http\Controllers\Api\V1\FeatureFlagController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/flags', [FeatureFlagController::class, 'index']);
});
