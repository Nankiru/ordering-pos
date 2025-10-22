<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed default categories, items, admin and demo user
        $this->call([
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\ItemSeeder::class,
            \Database\Seeders\AdminSeeder::class,
            \Database\Seeders\UserSeeder::class,
        ]);
    }
}
