<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            return response()->json([
                'user' => [
                    'name' => $user->nickname,
                    'ava' => strtoupper(mb_substr($user->nickname, 0, 1)),
                ],
            ]);
        }

        return redirect()->intended('/');
    }
}
