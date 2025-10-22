<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'email' => 'customer@example.com',
        ], [
            'name' => 'Demo Customer',
            'password' => Hash::make('password123'),
        ]);
    }
}
