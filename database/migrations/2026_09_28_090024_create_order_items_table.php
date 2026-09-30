<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Order snapshot (spec section 22): names + prices as they were at
     * purchase time. Deliberately has NO foreign keys to product/fragrance/
     * bottle/cap masters -- if "Premium Gold Cap" price changes later, this
     * row must not change with it.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('product_type', ['READY_MADE', 'CUSTOM_PERFUME']);
            $table->string('product_name');
            $table->string('size_name');
            $table->string('fragrance_name')->nullable();
            $table->string('bottle_name')->nullable();
            $table->string('cap_name')->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('base_price', 10, 2)->unsigned();
            $table->decimal('bottle_price', 10, 2)->unsigned()->default(0);
            $table->decimal('cap_price', 10, 2)->unsigned()->default(0);
            $table->decimal('discount', 10, 2)->unsigned()->default(0);
            $table->decimal('tax', 10, 2)->unsigned()->default(0);
            $table->decimal('final_price', 10, 2)->unsigned();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
