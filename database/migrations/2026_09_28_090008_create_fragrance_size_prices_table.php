<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Server-authoritative custom perfume base price by fragrance + size. */
    public function up(): void
    {
        Schema::create('fragrance_size_prices', function (Blueprint $table): void {
            $table->id();
            $table->string('fragrance_id');
            $table->string('size_id');
            $table->decimal('base_price', 10, 2)->unsigned();
            $table->timestamps();

            $table->foreign('fragrance_id')->references('id')->on('fragrances')->cascadeOnDelete();
            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
            $table->unique(['fragrance_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrance_size_prices');
    }
};
