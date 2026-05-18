<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleEmailRoutes
{
    private const LIMITS = [
        'forgot-password' => ['max' => 5, 'decay' => 60],
        'email/verification-notification' => ['max' => 3, 'decay' => 60],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = ltrim($request->path(), '/');

        if (! $request->isMethod('POST') || ! isset(self::LIMITS[$path])) {
            return $next($request);
        }

        $limit = self::LIMITS[$path];
        $key = $path.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, $limit['max'])) {
            return response()->json([
                'message' => 'Zu viele Versuche. Bitte warte einen Moment.',
            ], 429);
        }

        RateLimiter::hit($key, $limit['decay']);

        return $next($request);
    }
}
