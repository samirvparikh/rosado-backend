<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'id' => 'PRD-ROYAL-OUD', 'sku' => 'RSD-RO-001', 'name' => 'ROSADO Royal Oud', 'slug' => 'royal-oud',
                'shortDescription' => 'Woody oud with saffron and warm amber.',
                'description' => 'Royal Oud is a composed evening fragrance: dry woods, a thread of rose, and a long amber-musk trail. Sized independently so each concentration can be priced and stocked on its own.',
                'rating' => 4.8, 'reviewCount' => 128, 'flags' => ['isBestSeller', 'isFeatured', 'isTrending'],
                'fragranceId' => 'FRG001',
                'audiences' => ['AUD-MEN'], 'families' => ['FAM-WOODY', 'FAM-OUD', 'FAM-SPICY'],
                'occasions' => ['OCC-DATE', 'OCC-WEDDING', 'OCC-PARTY'], 'seasons' => ['SEA-WINTER', 'SEA-ALL'],
                'times' => ['TOD-NIGHT', 'TOD-EVENING'], 'intensities' => ['INT-STRONG'], 'longevities' => ['LON-12'],
                'characters' => ['CHR-WARM', 'CHR-LONG'], 'collections' => ['COL-SIGNATURE'],
            ],
            [
                'id' => 'PRD-VELVET-ROSE', 'sku' => 'RSD-VR-002', 'name' => 'ROSADO Velvet Rose', 'slug' => 'velvet-rose',
                'shortDescription' => 'Silk rose, jasmine and sandalwood.',
                'description' => 'A floral composed for celebrations. Velvet Rose opens with neroli, blooms into rose and jasmine, and settles into sandalwood musk.',
                'rating' => 4.7, 'reviewCount' => 96, 'flags' => ['isNewArrival', 'isBestSeller', 'isFeatured'],
                'fragranceId' => 'FRG002',
                'audiences' => ['AUD-WOMEN'], 'families' => ['FAM-FLORAL'],
                'occasions' => ['OCC-WEDDING', 'OCC-DATE'], 'seasons' => ['SEA-SPRING'],
                'times' => ['TOD-EVENING'], 'intensities' => ['INT-MODERATE'], 'longevities' => ['LON-8'],
                'characters' => ['CHR-SOFT'], 'collections' => ['COL-BLOOM'],
            ],
            [
                'id' => 'PRD-AMBER-NOIR', 'sku' => 'RSD-AN-003', 'name' => 'ROSADO Amber Noir', 'slug' => 'amber-noir',
                'shortDescription' => 'Saffron, dark amber and oud.',
                'description' => 'Amber Noir is nocturnal and concentrated. Saffron and pink pepper, iris in the heart, amber and oud in the base.',
                'rating' => 4.9, 'reviewCount' => 74, 'flags' => ['isBestSeller', 'isFeatured', 'isLimitedEdition', 'isTrending'],
                'fragranceId' => 'FRG004',
                'audiences' => ['AUD-MEN'], 'families' => ['FAM-AMBER', 'FAM-ORIENTAL'],
                'occasions' => ['OCC-FORMAL', 'OCC-SPECIAL'], 'seasons' => ['SEA-WINTER'],
                'times' => ['TOD-NIGHT'], 'intensities' => ['INT-INTENSE'], 'longevities' => ['LON-12'],
                'characters' => ['CHR-BOLD'], 'collections' => ['COL-NOIR'],
            ],
            [
                'id' => 'PRD-CITRUS-VEIL', 'sku' => 'RSD-CV-004', 'name' => 'ROSADO Citrus Veil', 'slug' => 'citrus-veil',
                'shortDescription' => 'Bergamot, lemon and clean musk.',
                'description' => 'A daylight citrus for office and everyday wear. Bright, precise, never sweet.',
                'rating' => 4.5, 'reviewCount' => 61, 'flags' => ['isNewArrival', 'isFeatured', 'isSale'],
                'discountType' => 'FLAT', 'discountValue' => 50,
                'fragranceId' => 'FRG003',
                'audiences' => ['AUD-UNISEX'], 'families' => ['FAM-CITRUS', 'FAM-FRESH'],
                'occasions' => ['OCC-OFFICE', 'OCC-EVERYDAY'], 'seasons' => ['SEA-SUMMER'],
                'times' => ['TOD-DAY'], 'intensities' => ['INT-LIGHT'], 'longevities' => ['LON-4'],
                'characters' => ['CHR-FRESH'], 'collections' => ['COL-ATELIER'],
            ],
            [
                'id' => 'PRD-SALT-AIR', 'sku' => 'RSD-SA-005', 'name' => 'ROSADO Salt Air', 'slug' => 'salt-air',
                'shortDescription' => 'Mineral sea salt, neroli and cedar.',
                'description' => 'An aquatic for travel and summer. Open, mineral, quietly woody.',
                'rating' => 4.6, 'reviewCount' => 53, 'flags' => ['isNewArrival', 'isTrending'],
                'fragranceId' => 'FRG005',
                'audiences' => ['AUD-UNISEX'], 'families' => ['FAM-AQUATIC', 'FAM-FRESH'],
                'occasions' => ['OCC-TRAVEL', 'OCC-CASUAL'], 'seasons' => ['SEA-SUMMER'],
                'times' => ['TOD-ALL'], 'intensities' => ['INT-MODERATE'], 'longevities' => ['LON-8'],
                'characters' => [], 'collections' => ['COL-ATELIER'],
            ],
            [
                'id' => 'PRD-VANILLA-EMBER', 'sku' => 'RSD-VE-006', 'name' => 'ROSADO Vanilla Ember', 'slug' => 'vanilla-ember',
                'shortDescription' => 'Smoked vanilla and amber woods.',
                'description' => 'A gourmand without excess sugar. Vanilla, cinnamon and ember amber.',
                'rating' => 4.7, 'reviewCount' => 88, 'flags' => ['isBestSeller'],
                'fragranceId' => 'FRG006',
                'audiences' => ['AUD-UNISEX'], 'families' => ['FAM-SWEET', 'FAM-AMBER'],
                'occasions' => ['OCC-PARTY', 'OCC-SPECIAL'], 'seasons' => ['SEA-WINTER'],
                'times' => ['TOD-NIGHT'], 'intensities' => ['INT-STRONG'], 'longevities' => ['LON-8'],
                'characters' => [], 'collections' => ['COL-SIGNATURE'],
            ],
            [
                'id' => 'PRD-JASMINE-SILK', 'sku' => 'RSD-JS-007', 'name' => 'ROSADO Jasmine Silk', 'slug' => 'jasmine-silk',
                'shortDescription' => 'White floral, light and luminous.',
                'description' => 'Jasmine and neroli over a soft musk. Daylight florals for warm months.',
                'rating' => 4.4, 'reviewCount' => 41, 'flags' => [],
                'fragranceId' => null,
                'audiences' => ['AUD-WOMEN'], 'families' => ['FAM-FLORAL'],
                'occasions' => ['OCC-EVERYDAY'], 'seasons' => ['SEA-SUMMER'],
                'times' => ['TOD-DAY'], 'intensities' => ['INT-LIGHT'], 'longevities' => ['LON-4'],
                'characters' => [], 'collections' => ['COL-BLOOM'],
            ],
            [
                'id' => 'PRD-PEONY-MIST', 'sku' => 'RSD-PM-008', 'name' => 'ROSADO Peony Mist', 'slug' => 'peony-mist',
                'shortDescription' => 'Soft peony and spring greens.',
                'description' => 'A light floral-fresh composition for spring and casual days.',
                'rating' => 4.3, 'reviewCount' => 29, 'flags' => ['isNewArrival'],
                'fragranceId' => null,
                'audiences' => ['AUD-WOMEN'], 'families' => ['FAM-FLORAL', 'FAM-FRESH'],
                'occasions' => ['OCC-CASUAL', 'OCC-EVERYDAY'], 'seasons' => ['SEA-SPRING'],
                'times' => ['TOD-DAY'], 'intensities' => ['INT-LIGHT'], 'longevities' => ['LON-4'],
                'characters' => [], 'collections' => ['COL-BLOOM'],
            ],
            [
                'id' => 'PRD-CEDAR-NIGHT', 'sku' => 'RSD-CN-009', 'name' => 'ROSADO Cedar Night', 'slug' => 'cedar-night',
                'shortDescription' => 'Dry cedar and evening woods.',
                'description' => 'Cedar, lavender and musk. A quieter woody for evening wear.',
                'rating' => 4.5, 'reviewCount' => 37, 'flags' => ['isFeatured'],
                'fragranceId' => null,
                'audiences' => ['AUD-MEN'], 'families' => ['FAM-WOODY'],
                'occasions' => ['OCC-DATE', 'OCC-FORMAL'], 'seasons' => ['SEA-WINTER'],
                'times' => ['TOD-EVENING'], 'intensities' => ['INT-MODERATE'], 'longevities' => ['LON-8'],
                'characters' => [], 'collections' => ['COL-NOIR'],
            ],
        ];

        $flagDefaults = [
            'isNewArrival' => false, 'isBestSeller' => false, 'isFeatured' => false,
            'isLimitedEdition' => false, 'isTrending' => false, 'isSale' => false,
        ];

        foreach ($products as $data) {
            $flags = $flagDefaults;
            foreach ($data['flags'] as $flag) {
                $flags[$flag] = true;
            }

            $product = Product::updateOrCreate(['id' => $data['id']], [
                'sku' => $data['sku'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'product_type' => 'READY_MADE',
                'short_description' => $data['shortDescription'],
                'description' => $data['description'],
                'brand' => 'ROSADO',
                'status' => 'ACTIVE',
                'tax_rate' => 18,
                'discount_type' => $data['discountType'] ?? 'NONE',
                'discount_value' => $data['discountValue'] ?? 0,
                'rating' => $data['rating'],
                'review_count' => $data['reviewCount'],
                'is_new_arrival' => $flags['isNewArrival'],
                'is_best_seller' => $flags['isBestSeller'],
                'is_featured' => $flags['isFeatured'],
                'is_limited_edition' => $flags['isLimitedEdition'],
                'is_trending' => $flags['isTrending'],
                'is_sale' => $flags['isSale'],
                'fragrance_id' => $data['fragranceId'],
            ]);

            $classificationIds = array_merge(
                $data['audiences'], $data['families'], $data['occasions'], $data['seasons'],
                $data['times'], $data['intensities'], $data['longevities'], $data['characters'],
                $data['collections'],
            );
            $product->classifications()->sync($classificationIds);
        }

        $images = [
            ['IMG-RO-1', 'PRD-ROYAL-OUD', 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Royal Oud bottle'],
            ['IMG-RO-2', 'PRD-ROYAL-OUD', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=1200&q=80', 'GALLERY', 2, false, 'Royal Oud lifestyle'],
            ['IMG-RO-3', 'PRD-ROYAL-OUD', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?auto=format&fit=crop&w=1200&q=80', 'PACKAGING', 3, false, 'Royal Oud packaging'],
            ['IMG-VR-1', 'PRD-VELVET-ROSE', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Velvet Rose bottle'],
            ['IMG-VR-2', 'PRD-VELVET-ROSE', 'https://images.unsplash.com/photo-1587017539504-67cfbddac569?auto=format&fit=crop&w=1200&q=80', 'LIFESTYLE', 2, false, 'Velvet Rose lifestyle'],
            ['IMG-AN-1', 'PRD-AMBER-NOIR', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Amber Noir bottle'],
            ['IMG-AN-2', 'PRD-AMBER-NOIR', 'https://images.unsplash.com/photo-1523293182086-7651a91dcd38?auto=format&fit=crop&w=1200&q=80', 'DETAIL', 2, false, 'Amber Noir detail'],
            ['IMG-CV-1', 'PRD-CITRUS-VEIL', 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Citrus Veil bottle'],
            ['IMG-SA-1', 'PRD-SALT-AIR', 'https://images.unsplash.com/photo-1523293182086-7651a91dcd38?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Salt Air bottle'],
            ['IMG-VE-1', 'PRD-VANILLA-EMBER', 'https://images.unsplash.com/photo-1587017539504-67cfbddac569?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Vanilla Ember bottle'],
            ['IMG-JS-1', 'PRD-JASMINE-SILK', 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Jasmine Silk bottle'],
            ['IMG-PM-1', 'PRD-PEONY-MIST', 'https://images.unsplash.com/photo-1611930022073-b7a4ba5accb7?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Peony Mist bottle'],
            ['IMG-CN-1', 'PRD-CEDAR-NIGHT', 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=1200&q=80', 'MAIN', 1, true, 'ROSADO Cedar Night bottle'],
        ];

        foreach ($images as [$code, $productId, $url, $type, $sortOrder, $isPrimary, $alt]) {
            ProductImage::updateOrCreate(
                ['product_id' => $productId, 'image_url' => $url],
                ['image_type' => $type, 'sort_order' => $sortOrder, 'is_primary' => $isPrimary, 'status' => 'ACTIVE', 'alt' => $alt],
            );
        }

        $sizeRows = [
            ['PRD-ROYAL-OUD', 'SIZE30', 'RSD-RO-30', 349, 299, 24],
            ['PRD-ROYAL-OUD', 'SIZE50', 'RSD-RO-50', 549, 499, 40],
            ['PRD-ROYAL-OUD', 'SIZE100', 'RSD-RO-100', 899, 799, 18],
            ['PRD-VELVET-ROSE', 'SIZE30', 'RSD-VR-30', 379, 329, 20],
            ['PRD-VELVET-ROSE', 'SIZE50', 'RSD-VR-50', 579, 529, 32],
            ['PRD-VELVET-ROSE', 'SIZE100', 'RSD-VR-100', 929, 849, 14],
            ['PRD-AMBER-NOIR', 'SIZE30', 'RSD-AN-30', 399, 349, 16],
            ['PRD-AMBER-NOIR', 'SIZE50', 'RSD-AN-50', 649, 599, 22],
            ['PRD-AMBER-NOIR', 'SIZE100', 'RSD-AN-100', 999, 929, 10],
            ['PRD-CITRUS-VEIL', 'SIZE30', 'RSD-CV-30', 329, 249, 30],
            ['PRD-CITRUS-VEIL', 'SIZE50', 'RSD-CV-50', 449, 349, 28],
            ['PRD-CITRUS-VEIL', 'SIZE100', 'RSD-CV-100', 699, 549, 16],
            ['PRD-SALT-AIR', 'SIZE30', 'RSD-SA-30', 329, 279, 22],
            ['PRD-SALT-AIR', 'SIZE50', 'RSD-SA-50', 479, 429, 26],
            ['PRD-SALT-AIR', 'SIZE100', 'RSD-SA-100', 749, 679, 12],
            ['PRD-VANILLA-EMBER', 'SIZE30', 'RSD-VE-30', 359, 309, 18],
            ['PRD-VANILLA-EMBER', 'SIZE50', 'RSD-VE-50', 529, 479, 24],
            ['PRD-VANILLA-EMBER', 'SIZE100', 'RSD-VE-100', 849, 769, 11],
            ['PRD-JASMINE-SILK', 'SIZE30', 'RSD-JS-30', 319, 289, 20],
            ['PRD-JASMINE-SILK', 'SIZE50', 'RSD-JS-50', 469, 419, 18],
            ['PRD-PEONY-MIST', 'SIZE30', 'RSD-PM-30', 309, 269, 16],
            ['PRD-PEONY-MIST', 'SIZE50', 'RSD-PM-50', 449, 399, 14],
            ['PRD-CEDAR-NIGHT', 'SIZE50', 'RSD-CN-50', 499, 449, 20],
            ['PRD-CEDAR-NIGHT', 'SIZE100', 'RSD-CN-100', 799, 729, 9],
        ];

        foreach ($sizeRows as [$productId, $sizeId, $sku, $mrp, $sellingPrice, $stock]) {
            ProductSize::updateOrCreate(
                ['product_id' => $productId, 'size_id' => $sizeId],
                [
                    'sku' => $sku,
                    'mrp' => $mrp,
                    'selling_price' => $sellingPrice,
                    'cost_price' => round($sellingPrice * 0.45),
                    'stock' => $stock,
                    'status' => 'ACTIVE',
                ],
            );
        }
    }
}
