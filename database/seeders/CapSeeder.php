<?php

namespace Database\Seeders;

use App\Models\Cap;
use App\Models\CapInventory;
use Illuminate\Database\Seeder;

class CapSeeder extends Seeder
{
    public function run(): void
    {
        $caps = [
            ['CAP001', 'Classic Black', '/images/caps/cap-classic-black.png', 0, 80, 1],
            ['CAP002', 'Premium Gold', '/images/caps/cap-premium-gold.png', 50, 42, 2],
            ['CAP003', 'Modern Silver', '/images/caps/cap-modern-silver.png', 30, 50, 3],
        ];

        foreach ($caps as [$id, $name, $image, $additionalPrice, $stock, $sortOrder]) {
            Cap::updateOrCreate(['id' => $id], [
                'name' => $name,
                'code' => $id,
                'image' => $image,
                'additional_price' => $additionalPrice,
                'stock' => $stock,
                'status' => 'ACTIVE',
                'sort_order' => $sortOrder,
            ]);

            CapInventory::updateOrCreate(['cap_id' => $id], [
                'current_stock' => $stock,
                'reserved_stock' => 0,
                'available_stock' => $stock,
                'reorder_level' => 8,
                'status' => 'ACTIVE',
            ]);
        }

        // V1: no cap_size_mappings rows -- every cap is available for every size
        // until compatibility rules are configured (spec section 15).
    }
}
