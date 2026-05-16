<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MainUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Nataliya',
            'last_name' => 'Tarasyuk',
            'email' => 'test@hsp-connect.com',
            'password' => Hash::make('password'),
            'nickname' => 'test_user',
        ]);
    }
}
