<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fragrance_notes', function (Blueprint $table): void {
            $table->id();
            $table->string('fragrance_id');
            $table->string('note_id');
            $table->enum('note_type', ['TOP', 'HEART', 'BASE']);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('fragrance_id')->references('id')->on('fragrances')->cascadeOnDelete();
            $table->foreign('note_id')->references('id')->on('notes')->cascadeOnDelete();
            $table->unique(['fragrance_id', 'note_id', 'note_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fragrance_notes');
    }
};
