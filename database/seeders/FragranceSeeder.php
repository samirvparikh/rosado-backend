<?php

namespace Database\Seeders;

use App\Models\Fragrance;
use App\Models\FragranceNote;
use App\Models\FragranceSizePrice;
use App\Models\Note;
use Illuminate\Database\Seeder;

class FragranceSeeder extends Seeder
{
    public function run(): void
    {
        $notes = [
            ['NOTE-BERGAMOT', 'Bergamot', 'TOP'],
            ['NOTE-LEMON', 'Lemon', 'TOP'],
            ['NOTE-PINK-PEPPER', 'Pink Pepper', 'TOP'],
            ['NOTE-SAFFRON', 'Saffron', 'TOP'],
            ['NOTE-NEROLI', 'Neroli', 'TOP'],
            ['NOTE-SEA-SALT', 'Sea Salt', 'TOP'],
            ['NOTE-ROSE', 'Rose', 'HEART'],
            ['NOTE-JASMINE', 'Jasmine', 'HEART'],
            ['NOTE-CINNAMON', 'Cinnamon', 'HEART'],
            ['NOTE-IRIS', 'Iris', 'HEART'],
            ['NOTE-PEONY', 'Peony', 'HEART'],
            ['NOTE-LAVENDER', 'Lavender', 'HEART'],
            ['NOTE-OUD', 'Oud', 'BASE'],
            ['NOTE-AMBER', 'Amber', 'BASE'],
            ['NOTE-MUSK', 'Musk', 'BASE'],
            ['NOTE-CEDAR', 'Cedarwood', 'BASE'],
            ['NOTE-VANILLA', 'Vanilla', 'BASE'],
            ['NOTE-SANDAL', 'Sandalwood', 'BASE'],
        ];

        foreach ($notes as [$id, $name, $type]) {
            Note::updateOrCreate(['id' => $id], ['name' => $name, 'note_type' => $type, 'status' => 'ACTIVE']);
        }

        $fragrances = [
            [
                'id' => 'FRG001', 'name' => 'Woody Oud', 'slug' => 'woody-oud',
                'short_description' => 'Woody · Warm · Long Lasting',
                'description' => 'A composed oud built on dry woods, warm spice and a lingering amber-musk trail. The signature ROSADO custom fragrance.',
                'gender' => 'UNISEX',
                'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-WOODY', 'FAM-OUD'], 'characters' => ['CHR-WARM', 'CHR-LONG'],
            ],
            [
                'id' => 'FRG002', 'name' => 'Velvet Rose', 'slug' => 'velvet-rose',
                'short_description' => 'Floral · Soft · Romantic',
                'description' => 'Petal-rich rose with a silk musk dry-down. Made for evenings and celebrations.',
                'gender' => 'WOMEN',
                'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-FLORAL'], 'characters' => ['CHR-SOFT'],
            ],
            [
                'id' => 'FRG003', 'name' => 'Citrus Veil', 'slug' => 'citrus-veil',
                'short_description' => 'Citrus · Fresh · Daytime',
                'description' => 'Bright bergamot and lemon over a clean musk. An everyday atelier citrus.',
                'gender' => 'UNISEX',
                'image' => 'https://images.unsplash.com/photo-1615634260167-c8cdede054de?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-CITRUS', 'FAM-FRESH'], 'characters' => ['CHR-FRESH'],
            ],
            [
                'id' => 'FRG004', 'name' => 'Amber Noir', 'slug' => 'amber-noir',
                'short_description' => 'Oriental · Bold · Night',
                'description' => 'Dark amber, saffron and woods. A nocturnal, concentrated composition.',
                'gender' => 'MEN',
                'image' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-AMBER', 'FAM-ORIENTAL'], 'characters' => ['CHR-BOLD', 'CHR-WARM'],
            ],
            [
                'id' => 'FRG005', 'name' => 'Salt Air', 'slug' => 'salt-air',
                'short_description' => 'Aquatic · Mineral · Open',
                'description' => 'Sea salt, neroli and cedar. A mineral freshness for travel and summer.',
                'gender' => 'UNISEX',
                'image' => 'https://images.unsplash.com/photo-1523293182086-7651a91dcd38?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-AQUATIC', 'FAM-FRESH'], 'characters' => ['CHR-FRESH'],
            ],
            [
                'id' => 'FRG006', 'name' => 'Vanilla Ember', 'slug' => 'vanilla-ember',
                'short_description' => 'Sweet · Warm · Gourmand',
                'description' => 'Smoked vanilla, amber and soft woods. Comfort with a late-night glow.',
                'gender' => 'UNISEX',
                'image' => 'https://images.unsplash.com/photo-1587017539504-67cfbddac569?auto=format&fit=crop&w=900&q=80',
                'families' => ['FAM-SWEET', 'FAM-AMBER'], 'characters' => ['CHR-WARM'],
            ],
        ];

        foreach ($fragrances as $data) {
            $fragrance = Fragrance::updateOrCreate(['id' => $data['id']], [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'gender' => $data['gender'],
                'image' => $data['image'],
                'status' => 'ACTIVE',
            ]);
            $fragrance->classifications()->sync([...$data['families'], ...$data['characters']]);
        }

        $fragranceNotes = [
            ['FRG001', 'NOTE-BERGAMOT', 'TOP', 1], ['FRG001', 'NOTE-SAFFRON', 'TOP', 2],
            ['FRG001', 'NOTE-ROSE', 'HEART', 1], ['FRG001', 'NOTE-CINNAMON', 'HEART', 2],
            ['FRG001', 'NOTE-OUD', 'BASE', 1], ['FRG001', 'NOTE-AMBER', 'BASE', 2], ['FRG001', 'NOTE-MUSK', 'BASE', 3],
            ['FRG002', 'NOTE-NEROLI', 'TOP', 1],
            ['FRG002', 'NOTE-ROSE', 'HEART', 1], ['FRG002', 'NOTE-JASMINE', 'HEART', 2],
            ['FRG002', 'NOTE-MUSK', 'BASE', 1], ['FRG002', 'NOTE-SANDAL', 'BASE', 2],
            ['FRG003', 'NOTE-LEMON', 'TOP', 1], ['FRG003', 'NOTE-BERGAMOT', 'TOP', 2],
            ['FRG003', 'NOTE-NEROLI', 'HEART', 1],
            ['FRG003', 'NOTE-MUSK', 'BASE', 1],
            ['FRG004', 'NOTE-SAFFRON', 'TOP', 1], ['FRG004', 'NOTE-PINK-PEPPER', 'TOP', 2],
            ['FRG004', 'NOTE-IRIS', 'HEART', 1],
            ['FRG004', 'NOTE-AMBER', 'BASE', 1], ['FRG004', 'NOTE-OUD', 'BASE', 2],
            ['FRG005', 'NOTE-SEA-SALT', 'TOP', 1], ['FRG005', 'NOTE-LEMON', 'TOP', 2],
            ['FRG005', 'NOTE-NEROLI', 'HEART', 1],
            ['FRG005', 'NOTE-CEDAR', 'BASE', 1],
            ['FRG006', 'NOTE-PINK-PEPPER', 'TOP', 1],
            ['FRG006', 'NOTE-CINNAMON', 'HEART', 1],
            ['FRG006', 'NOTE-VANILLA', 'BASE', 1], ['FRG006', 'NOTE-AMBER', 'BASE', 2],
        ];

        foreach ($fragranceNotes as [$fragranceId, $noteId, $type, $sortOrder]) {
            FragranceNote::updateOrCreate(
                ['fragrance_id' => $fragranceId, 'note_id' => $noteId, 'note_type' => $type],
                ['sort_order' => $sortOrder],
            );
        }

        $sizePrices = [
            ['FRG001', 'SIZE30', 299], ['FRG001', 'SIZE50', 399], ['FRG001', 'SIZE100', 649],
            ['FRG002', 'SIZE30', 289], ['FRG002', 'SIZE50', 429], ['FRG002', 'SIZE100', 679],
            ['FRG003', 'SIZE30', 249], ['FRG003', 'SIZE50', 349], ['FRG003', 'SIZE100', 549],
            ['FRG004', 'SIZE30', 319], ['FRG004', 'SIZE50', 469], ['FRG004', 'SIZE100', 729],
            ['FRG005', 'SIZE30', 259], ['FRG005', 'SIZE50', 369], ['FRG005', 'SIZE100', 579],
            ['FRG006', 'SIZE30', 279], ['FRG006', 'SIZE50', 389], ['FRG006', 'SIZE100', 619],
        ];

        foreach ($sizePrices as [$fragranceId, $sizeId, $price]) {
            FragranceSizePrice::updateOrCreate(
                ['fragrance_id' => $fragranceId, 'size_id' => $sizeId],
                ['base_price' => $price],
            );
        }
    }
}
