<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->with('tenant')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (! $user->tenant_id || ! $user->tenant->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your organization account is currently suspended or invalid.'],
            ]);
        }

        $token = $user->createToken('admin-dashboard-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('roles', 'permissions'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
