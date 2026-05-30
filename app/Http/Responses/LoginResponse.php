<?php

namespace App\Http\Responses;

use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $user = Auth::user() ?? $request->user();

        ActivityLogger::log('login.success', 'User logged in: '.($user?->nickname ?? 'unknown'), $user?->id);

        if (! $user->hasVerifiedEmail()) {
            $key = 'resend-verification:'.$user->id;
            if (! RateLimiter::tooManyAttempts($key, maxAttempts: 2)) {
                $user->sendEmailVerificationNotification();
                RateLimiter::hit($key, decaySeconds: 300);
            }
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json(['needs_verification' => true], 200);
            }

            return redirect('/');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'user' => [
                    'name' => $user->nickname,
                    'ava' => strtoupper(mb_substr($user->nickname, 0, 1)),
                ],
            ]);
        }

        $intended = session()->pull('url.intended', '/');
        $appUrl = rtrim(config('app.url'), '/');
        $safe = str_starts_with($intended, $appUrl) || str_starts_with($intended, '/');

        return redirect($safe ? $intended : '/');
    }
}
