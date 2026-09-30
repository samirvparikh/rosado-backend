<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** A ready-made product can have multiple sizes, each independently priced/stocked. */
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $table): void {
            $table->id();
            $table->string('product_id');
            $table->string('size_id');
            $table->string('sku')->unique();
            $table->decimal('mrp', 10, 2)->unsigned();
            $table->decimal('selling_price', 10, 2)->unsigned();
            $table->decimal('cost_price', 10, 2)->unsigned();
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
            $table->unique(['product_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }
};
