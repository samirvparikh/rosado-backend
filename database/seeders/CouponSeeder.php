<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(['code' => 'ROSADO10'], ['type' => 'PERCENT', 'value' => 10, 'min_subtotal' => 799, 'status' => 'ACTIVE']);
        Coupon::updateOrCreate(['code' => 'WELCOME50'], ['type' => 'FLAT', 'value' => 50, 'min_subtotal' => 499, 'status' => 'ACTIVE']);
    }
}
