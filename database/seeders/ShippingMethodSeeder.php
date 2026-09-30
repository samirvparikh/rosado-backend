<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        ShippingMethod::updateOrCreate(['id' => 'standard'], [
            'name' => 'Standard', 'description' => 'Complimentary above ₹999', 'price' => 79,
            'eta' => '4–6 days', 'sort_order' => 1, 'status' => 'ACTIVE',
        ]);

        ShippingMethod::updateOrCreate(['id' => 'express'], [
            'name' => 'Express', 'description' => 'Priority dispatch', 'price' => 149,
            'eta' => '1–2 days', 'sort_order' => 2, 'status' => 'ACTIVE',
        ]);
    }
}
