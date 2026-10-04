<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The bundled bottle/cap artwork now ships as transparent PNGs. Repoint any
 * row still using the old bundled SVG paths; uploaded images are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->swap('.svg', '.png');
    }

    public function down(): void
    {
        $this->swap('.png', '.svg');
    }

    private function swap(string $from, string $to): void
    {
        foreach (['bottles' => '/images/bottles/', 'caps' => '/images/caps/'] as $table => $prefix) {
            DB::table($table)
                ->where('image', 'like', $prefix.'%'.$from)
                ->update(['image' => DB::raw("CONCAT(LEFT(image, CHAR_LENGTH(image) - 4), '{$to}')")]);
        }
    }
};
