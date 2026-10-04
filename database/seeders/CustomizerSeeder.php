<?php

namespace Database\Seeders;

use App\Models\Bottle;
use App\Models\Cap;
use App\Models\CustomizerLayerOverride;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

/**
 * Aligns the bundled bottle/cap artwork (public/images/bottles, /caps on the
 * storefront) on the 3:4 customizer canvas, and creates the default
 * "ROSADO Signature" custom product. Safe to re-run: it only touches the
 * seeded component IDs below and never overwrites an existing product.
 *
 * Geometry of the bundled PNGs (rendered at 3x / 4x from 200- and 120-unit
 * artboards): bottles are 200 units wide with the neck in the top 26 units;
 * caps are 120 units wide. Canvas is 600 x 800. Only ratios matter here.
 */
class CustomizerSeeder extends Seeder
{
    private const CANVAS_W = 600;

    private const CANVAS_H = 800;

    /** Bottle base line, so every bottle stands on the same "shelf". */
    private const FLOOR_Y = 720;

    /** id => [svg height, size ML] */
    private const BOTTLES = [
        'BTL001' => [210, 30],
        'BTL002' => [270, 50],
        'BTL003' => [270, 50],
        'BTL004' => [300, 100],
        'BTL005' => [320, 100],
        'BTL006' => [214, 30],
    ];

    /** id => svg height (width 120) */
    private const CAPS = ['CAP001' => 100, 'CAP002' => 110, 'CAP003' => 112];

    public function run(): void
    {
        $geometry = [];
        foreach (self::BOTTLES as $id => [$svgHeight, $sizeMl]) {
            $width = match (true) {
                $sizeMl <= 30 => 240,
                $sizeMl <= 50 => 276,
                default => 324,
            };
            $scale = $width / 200;
            $top = self::FLOOR_Y - $svgHeight * $scale;
            $geometry[$id] = compact('width', 'scale', 'top', 'svgHeight');

            Bottle::where('id', $id)->update([
                'layer_top' => $this->pctY($top),
                'layer_left' => $this->pctX((self::CANVAS_W - $width) / 2),
                'layer_width' => $this->pctX($width),
                'layer_z' => 10,
                'label_top' => $this->pctY($top + 0.58 * $svgHeight * $scale),
                'label_left' => 50,
                'label_width' => $this->pctX($width * 0.62),
            ]);
        }

        $capBox = function (array $bottle, int $capSvgHeight): array {
            $width = $bottle['width'] * 0.36;
            $height = $width * $capSvgHeight / 120;
            // Cap's lower edge covers the neck down to the collar.
            $bottom = $bottle['top'] + 26 * $bottle['scale'];

            return [
                'layer_top' => $this->pctY($bottom - $height),
                'layer_left' => $this->pctX((self::CANVAS_W - $width) / 2),
                'layer_width' => $this->pctX($width),
                'layer_z' => 30,
            ];
        };

        foreach (self::CAPS as $capId => $capSvgHeight) {
            // Default position = fitted to the 50 ML Premium Glass bottle...
            Cap::where('id', $capId)->update($capBox($geometry['BTL002'], $capSvgHeight));

            // ...and an exact fit for every bundled bottle.
            foreach ($geometry as $bottleId => $bottle) {
                if (! Bottle::whereKey($bottleId)->exists() || ! Cap::whereKey($capId)->exists()) {
                    continue;
                }
                CustomizerLayerOverride::updateOrCreate(
                    ['bottle_id' => $bottleId, 'layer_type' => 'CAP', 'layer_id' => $capId],
                    $capBox($bottle, $capSvgHeight),
                );
            }
        }

        if (! Product::where('product_type', 'CUSTOM_PERFUME')->exists()) {
            $product = Product::create([
                'id' => 'PRD-SIGNATURE',
                'sku' => 'RSD-SIG-CUSTOM',
                'name' => 'ROSADO Signature',
                'slug' => 'signature',
                'product_type' => 'CUSTOM_PERFUME',
                'short_description' => 'Your perfume, composed your way: fragrance, bottle and cap.',
                'description' => 'Choose the size, the fragrance, the bottle and the cap -- then print your own words on the label. Every Signature perfume is assembled to order.',
                'brand' => 'ROSADO',
                'status' => 'ACTIVE',
                'in_stock' => true,
                'is_featured' => true,
            ]);

            // Base price per size. 0 keeps the perfume cost in each fragrance's
            // per-size price; raise these in Products > Sizes & Pricing.
            foreach (['SIZE30' => 'RSD-SIG-30', 'SIZE50' => 'RSD-SIG-50', 'SIZE100' => 'RSD-SIG-100'] as $sizeId => $sku) {
                ProductSize::firstOrCreate(
                    ['product_id' => $product->id, 'size_id' => $sizeId],
                    ['sku' => $sku, 'mrp' => 0, 'selling_price' => 0, 'cost_price' => 0, 'stock' => 0, 'status' => 'ACTIVE'],
                );
            }
        }
    }

    private function pctX(float $px): float
    {
        return round($px / self::CANVAS_W * 100, 2);
    }

    private function pctY(float $px): float
    {
        return round($px / self::CANVAS_H * 100, 2);
    }
}
