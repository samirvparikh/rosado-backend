<?php

namespace Database\Seeders;

use App\Models\Bottle;
use App\Models\BottleInventory;
use Illuminate\Database\Seeder;

class BottleSeeder extends Seeder
{
    public function run(): void
    {
        // Transparent, capless artwork: the builder preview layers the chosen cap and label on top.
        $bottles = [
            ['BTL001', 'Atelier Clear 30 ML', 'SIZE30', '/images/bottles/atelier-clear-30.svg', 0, 40, 1],
            ['BTL002', 'Premium Glass 50 ML', 'SIZE50', '/images/bottles/premium-glass-50.svg', 50, 28, 1],
            ['BTL003', 'Classic Glass 50 ML', 'SIZE50', '/images/bottles/classic-glass-50.svg', 0, 36, 2],
            ['BTL004', 'Sculpted Crystal 100 ML', 'SIZE100', '/images/bottles/sculpted-crystal-100.svg', 120, 18, 1],
            ['BTL005', 'Noir Column 100 ML', 'SIZE100', '/images/bottles/noir-column-100.svg', 80, 22, 2],
            ['BTL006', 'Archive Flacon 30 ML', 'SIZE30', '/images/bottles/archive-flacon-30.svg', 30, 16, 2],
        ];

        foreach ($bottles as [$id, $name, $sizeId, $image, $additionalPrice, $stock, $sortOrder]) {
            Bottle::updateOrCreate(['id' => $id], [
                'name' => $name,
                'code' => $id,
                'image' => $image,
                'size_id' => $sizeId,
                'additional_price' => $additionalPrice,
                'stock' => $stock,
                'status' => 'ACTIVE',
                'sort_order' => $sortOrder,
            ]);

            BottleInventory::updateOrCreate(['bottle_id' => $id], [
                'current_stock' => $stock,
                'reserved_stock' => 0,
                'available_stock' => $stock,
                'reorder_level' => 5,
                'status' => 'ACTIVE',
            ]);
        }
    }
}
