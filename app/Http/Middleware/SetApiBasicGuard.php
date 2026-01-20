<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Add api-basic guard to request
 */
class SetApiBasicGuard
{
    public function handle($request, Closure $next)
    {
        Auth::shouldUse('api-basic');
        return $next($request);
    }
}