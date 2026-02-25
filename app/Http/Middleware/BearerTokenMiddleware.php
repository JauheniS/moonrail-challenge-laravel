<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BearerTokenMiddleware
{
    private const BEARER_TOKEN = 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';

    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() !== self::BEARER_TOKEN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
