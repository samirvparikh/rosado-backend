<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['id' => 'SIZE30', 'name' => '30 ML', 'size_ml' => 30, 'display_name' => '30 ML', 'sort_order' => 1],
            ['id' => 'SIZE50', 'name' => '50 ML', 'size_ml' => 50, 'display_name' => '50 ML', 'sort_order' => 2],
            ['id' => 'SIZE100', 'name' => '100 ML', 'size_ml' => 100, 'display_name' => '100 ML', 'sort_order' => 3],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(['id' => $size['id']], [...$size, 'status' => 'ACTIVE']);
        }
    }
}
