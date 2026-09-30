<?php

namespace Database\Seeders;

use App\Models\Bottle;
use App\Models\BottleInventory;
use Illuminate\Database\Seeder;

class BottleSeeder extends Seeder
{
    public function run(): void
    {
        $bottles = [
            ['BTL001', 'Atelier Clear 30 ML', 'SIZE30', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=700&q=80', 0, 40, 1],
            ['BTL002', 'Premium Glass 50 ML', 'SIZE50', 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=700&q=80', 50, 28, 1],
            ['BTL003', 'Classic Glass 50 ML', 'SIZE50', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?auto=format&fit=crop&w=700&q=80', 0, 36, 2],
            ['BTL004', 'Sculpted Crystal 100 ML', 'SIZE100', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=700&q=80', 120, 18, 1],
            ['BTL005', 'Noir Column 100 ML', 'SIZE100', 'https://images.unsplash.com/photo-1523293182086-7651a91dcd38?auto=format&fit=crop&w=700&q=80', 80, 22, 2],
            ['BTL006', 'Archive Flacon 30 ML', 'SIZE30', 'https://images.unsplash.com/photo-1587017539504-67cfbddac569?auto=format&fit=crop&w=700&q=80', 30, 16, 2],
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
