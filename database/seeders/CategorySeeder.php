<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::firstOrCreate(['name' => 'Pizza'], ['description' => 'Pizza category']);
        Category::firstOrCreate(['name' => 'Drinks'], ['description' => 'Drinks and beverages']);
    }
}
