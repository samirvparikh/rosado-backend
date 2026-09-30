<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bottle is a component of a Custom Perfume, never a standalone product.
     * Bottle.size_id is the field the builder filters on (Rule 5/6).
     */
    public function up(): void
    {
        Schema::create('bottles', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('image')->nullable();
            $table->string('size_id');
            $table->decimal('additional_price', 10, 2)->unsigned()->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottles');
    }
};
