<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'AUDIENCE' => [
                ['AUD-MEN', 'Men', 'men', 1],
                ['AUD-WOMEN', 'Women', 'women', 2],
                ['AUD-UNISEX', 'Unisex', 'unisex', 3],
            ],
            'FRAGRANCE_FAMILY' => [
                ['FAM-FRESH', 'Fresh', 'fresh', 1],
                ['FAM-WOODY', 'Woody', 'woody', 2],
                ['FAM-FLORAL', 'Floral', 'floral', 3],
                ['FAM-ORIENTAL', 'Oriental', 'oriental', 4],
                ['FAM-CITRUS', 'Citrus', 'citrus', 5],
                ['FAM-SWEET', 'Sweet / Gourmand', 'sweet', 6],
                ['FAM-MUSKY', 'Musky', 'musky', 7],
                ['FAM-OUD', 'Oud', 'oud', 8],
                ['FAM-FRUITY', 'Fruity', 'fruity', 9],
                ['FAM-AQUATIC', 'Aquatic', 'aquatic', 10],
                ['FAM-AMBER', 'Amber', 'amber', 11],
                ['FAM-SPICY', 'Spicy', 'spicy', 12],
                ['FAM-GREEN', 'Green', 'green', 13],
            ],
            'OCCASION' => [
                ['OCC-EVERYDAY', 'Everyday Wear', 'everyday-wear', 1],
                ['OCC-OFFICE', 'Office Wear', 'office-wear', 2],
                ['OCC-DATE', 'Date Night', 'date-night', 3],
                ['OCC-PARTY', 'Party & Events', 'party-events', 4],
                ['OCC-WEDDING', 'Wedding', 'wedding', 5],
                ['OCC-FORMAL', 'Formal', 'formal', 6],
                ['OCC-CASUAL', 'Casual', 'casual', 7],
                ['OCC-TRAVEL', 'Travel', 'travel', 8],
                ['OCC-SPECIAL', 'Special Occasions', 'special-occasions', 9],
            ],
            'SEASON' => [
                ['SEA-SUMMER', 'Summer', 'summer', 1],
                ['SEA-WINTER', 'Winter', 'winter', 2],
                ['SEA-MONSOON', 'Monsoon', 'monsoon', 3],
                ['SEA-SPRING', 'Spring', 'spring', 4],
                ['SEA-ALL', 'All Season', 'all-season', 5],
            ],
            'TIME_OF_DAY' => [
                ['TOD-DAY', 'Day', 'day', 1],
                ['TOD-EVENING', 'Evening', 'evening', 2],
                ['TOD-NIGHT', 'Night', 'night', 3],
                ['TOD-ALL', 'All Day', 'all-day', 4],
            ],
            'INTENSITY' => [
                ['INT-LIGHT', 'Light', 'light', 1],
                ['INT-MODERATE', 'Moderate', 'moderate', 2],
                ['INT-STRONG', 'Strong', 'strong', 3],
                ['INT-INTENSE', 'Intense', 'intense', 4],
            ],
            'LONGEVITY' => [
                ['LON-4', '4–6 Hours', '4-6-hours', 1],
                ['LON-8', '8+ Hours', '8-hours', 2],
                ['LON-12', '12+ Hours', '12-hours', 3],
            ],
            'SCENT_CHARACTER' => [
                ['CHR-WARM', 'Warm', 'warm', 1],
                ['CHR-FRESH', 'Fresh', 'fresh', 2],
                ['CHR-LONG', 'Long Lasting', 'long-lasting', 3],
                ['CHR-SOFT', 'Soft', 'soft', 4],
                ['CHR-BOLD', 'Bold', 'bold', 5],
            ],
            'COLLECTION' => [
                ['COL-SIGNATURE', 'Signature', 'signature', 1],
                ['COL-ATELIER', 'Atelier', 'atelier', 2],
                ['COL-NOIR', 'Noir', 'noir', 3],
                ['COL-BLOOM', 'Bloom', 'bloom', 4],
            ],
        ];

        foreach ($groups as $group => $rows) {
            foreach ($rows as [$id, $name, $slug, $sortOrder]) {
                Classification::updateOrCreate(['id' => $id], [
                    'group' => $group,
                    'name' => $name,
                    'slug' => $slug,
                    'sort_order' => $sortOrder,
                    'status' => 'ACTIVE',
                ]);
            }
        }
    }
}
