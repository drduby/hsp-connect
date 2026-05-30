<?php

namespace App\Http\Responses;

use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        ActivityLogger::log('user.registered', 'New user registered: '.($user?->nickname ?? $request->input('nickname')), $user?->id);

        if ($request->wantsJson()) {
            return response()->json(['needs_verification' => true], 201);
        }

        return redirect('/');
    }
}
