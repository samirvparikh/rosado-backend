<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_perfume_configurations', function (Blueprint $table): void {
            $table->id();
            $table->string('size_id');
            $table->string('fragrance_id');
            $table->string('bottle_id');
            $table->string('cap_id');
            $table->decimal('base_price', 10, 2)->unsigned();
            $table->decimal('bottle_price', 10, 2)->unsigned();
            $table->decimal('cap_price', 10, 2)->unsigned();
            $table->decimal('total_price', 10, 2)->unsigned();
            $table->enum('status', ['DRAFT', 'SAVED', 'ORDERED'])->default('DRAFT');
            $table->string('custom_name')->nullable();
            $table->text('personal_message')->nullable();
            $table->boolean('gift_packaging')->default(false);
            $table->timestamps();

            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
            $table->foreign('fragrance_id')->references('id')->on('fragrances')->cascadeOnDelete();
            $table->foreign('bottle_id')->references('id')->on('bottles')->cascadeOnDelete();
            $table->foreign('cap_id')->references('id')->on('caps')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_perfume_configurations');
    }
};
