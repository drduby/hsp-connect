<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:30', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique(User::class)],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', Rule::unique(User::class)],
            'password' => $this->passwordRules(),
        ], [
            'nickname.regex' => 'Der Nickname darf nur Buchstaben, Zahlen und _ enthalten.',
            'nickname.unique' => 'Dieser Nickname ist bereits vergeben.',
            'email.unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
        ])->validate();

        return User::create([
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'nickname' => $input['nickname'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
