<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Fragrance -> Fragrance Family / Scent Character (many-to-many, mirrors ProductClassification). */
    public function up(): void
    {
        Schema::create('fragrance_classifications', function (Blueprint $table): void {
            $table->id();
            $table->string('fragrance_id');
            $table->string('classification_id');
            $table->timestamps();

            $table->foreign('fragrance_id')->references('id')->on('fragrances')->cascadeOnDelete();
            $table->foreign('classification_id')->references('id')->on('classifications')->cascadeOnDelete();
            $table->unique(['fragrance_id', 'classification_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrance_classifications');
    }
};
