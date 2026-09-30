<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('sku')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('product_type', ['READY_MADE', 'CUSTOM_PERFUME', 'GIFT_SET', 'BUNDLE', 'LIMITED_EDITION']);
            $table->string('short_description');
            $table->text('description');
            $table->string('brand')->default('ROSADO');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            // Ready-made products price per size via product_sizes; these are
            // optional top-level fallbacks for non-sized product types.
            $table->decimal('base_price', 10, 2)->unsigned()->nullable();
            $table->decimal('sale_price', 10, 2)->unsigned()->nullable();
            $table->decimal('cost_price', 10, 2)->unsigned()->nullable();
            $table->decimal('mrp', 10, 2)->unsigned()->nullable();

            $table->decimal('tax_rate', 5, 2)->default(18);
            $table->enum('discount_type', ['PERCENT', 'FLAT', 'NONE'])->default('NONE');
            $table->decimal('discount_value', 10, 2)->default(0);

            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('review_count')->default(0);

            $table->boolean('is_new_arrival')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_limited_edition')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_sale')->default(false);

            // Signature fragrance shown on the PDP for notes/family display.
            $table->string('fragrance_id')->nullable();

            $table->timestamps();

            $table->foreign('fragrance_id')->references('id')->on('fragrances')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
