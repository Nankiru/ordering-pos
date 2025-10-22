<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;

class PromoCodeSeeder extends Seeder
{
    public function run()
    {
        PromoCode::updateOrCreate(['code' => 'SAVE10'], [
            'discount_type' => 'percent',
            'discount_value' => 10,
        ]);

        PromoCode::updateOrCreate(['code' => 'OFF5'], [
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
        ]);
    }
}
