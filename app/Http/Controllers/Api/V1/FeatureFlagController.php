<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $environment = $request->user();

        $flags = $environment->featureFlags()->pluck('is_enabled', 'key');

        return response()->json([
            'environment' => $environment->name,
            'flags' => $flags,
        ]);
    }
}
