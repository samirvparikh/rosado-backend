<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SizeSeeder::class,
            ClassificationSeeder::class,
            FragranceSeeder::class,
            BottleSeeder::class,
            CapSeeder::class,
            ProductSeeder::class,
            CustomizerSeeder::class,
            ShippingMethodSeeder::class,
            CouponSeeder::class,
        ]);
    }
}
