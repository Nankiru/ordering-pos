<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $pizza = Category::firstOrCreate(['name' => 'Pizza'], ['description' => 'Pizza category']);
        $drinks = Category::firstOrCreate(['name' => 'Drinks'], ['description' => 'Drinks and beverages']);

        Item::firstOrCreate([
            'name' => 'Margherita',
        ], [
            'category_id' => $pizza->id,
            'price' => 8.99,
            'description' => 'Classic cheese and tomato',
        ]);

        Item::firstOrCreate([
            'name' => 'Pepperoni',
        ], [
            'category_id' => $pizza->id,
            'price' => 9.99,
            'description' => 'Pepperoni and cheese',
        ]);

        Item::firstOrCreate([
            'name' => 'Coke',
        ], [
            'category_id' => $drinks->id,
            'price' => 1.99,
            'description' => 'Chilled cola',
        ]);
    }
}
