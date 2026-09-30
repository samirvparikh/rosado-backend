<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Cap is a component of a Custom Perfume, never a standalone product. */
    public function up(): void
    {
        Schema::create('caps', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('image')->nullable();
            $table->decimal('additional_price', 10, 2)->unsigned()->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caps');
    }
};
