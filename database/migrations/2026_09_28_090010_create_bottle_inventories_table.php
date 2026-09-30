<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottle_inventories', function (Blueprint $table): void {
            $table->string('bottle_id')->primary();
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('reserved_stock')->default(0);
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(5);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            $table->foreign('bottle_id')->references('id')->on('bottles')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottle_inventories');
    }
};
