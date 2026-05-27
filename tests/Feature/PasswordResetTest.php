<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('a password reset link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->postJson('/forgot-password', [
        'email' => $user->email,
    ])->assertSuccessful();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('the reset password link page is available', function () {
    $this->get(route('password.reset', [
        'token' => 'test-token',
        'email' => 'user@example.com',
    ]))
        ->assertSuccessful()
        ->assertSee('Passwort zur&#xFC;cksetzen', false)
        ->assertSee('user@example.com');
});

test('a password can be reset with a valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->postJson('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password1',
        'password_confirmation' => 'new-password1',
    ])->assertSuccessful();

    expect(Hash::check('new-password1', $user->refresh()->password))->toBeTrue();
});
