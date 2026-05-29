<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'first_name' => 'Gent',
                'last_name' => 'Myftiu',
                'email' => 'myftiu@hsp-connect.com',
                'nickname' => 'myftiu',
                'password' => 'Xk9#mP2$vL8@nQ4!',
            ],
            [
                'first_name' => 'Nataliya',
                'last_name' => 'Tarasyuk',
                'email' => 'tarasyuk@hsp-connect.com',
                'nickname' => 'tarasyuk',
                'password' => 'Rj5&wH3^bN7*cF6@',
            ],
        ];

        foreach ($admins as $admin) {
            User::query()->updateOrCreate(
                ['email' => $admin['email']],
                [
                    'first_name' => $admin['first_name'],
                    'last_name' => $admin['last_name'],
                    'nickname' => $admin['nickname'],
                    'password' => Hash::make($admin['password']),
                    'is_admin' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
