<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

class ThrottleRequests extends BaseThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $maxAttempts
     * @param  int  $decayMinutes
     * @param  string  $prefix
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}