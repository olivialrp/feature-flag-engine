<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->tenant_id) {
            abort(403, 'Unauthorized: No active tenant associated with this account.');
        }

        if (! $user->tenant->is_active) {
            abort(403, 'Unauthorized: Your organization account is currently suspended.');
        }

        return $next($request);
    }
}
