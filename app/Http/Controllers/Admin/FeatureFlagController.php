<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeatureFlagRequest;
use App\Http\Requests\Admin\UpdateFeatureFlagRequest;
use App\Models\FeatureFlag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FeatureFlagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', FeatureFlag::class);

        $flags = FeatureFlag::whereHas('environment.project', function ($query) use ($request) {
            $query->where('tenant_id', $request->user()->tenant_id);
        })->with('environment.project')->paginate(20);

        return response()->json($flags);
    }

    public function store(StoreFeatureFlagRequest $request): JsonResponse
    {
        $flag = FeatureFlag::create($request->validated());

        return response()->json($flag, 201);
    }

    public function show(FeatureFlag $featureFlag): JsonResponse
    {
        Gate::authorize('view', $featureFlag);

        return response()->json($featureFlag->load('environment.project'));
    }

    public function update(UpdateFeatureFlagRequest $request, FeatureFlag $featureFlag): JsonResponse
    {
        $featureFlag->update($request->validated());

        return response()->json($featureFlag);
    }

    public function destroy(FeatureFlag $featureFlag): JsonResponse
    {
        Gate::authorize('delete', $featureFlag);

        $featureFlag->delete();

        return response()->json(null, 204);
    }
}
