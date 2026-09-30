<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Optional cap/size compatibility (spec section 15). Empty = cap is
     * available for every size. Never hard-code this in application code.
     */
    public function up(): void
    {
        Schema::create('cap_size_mappings', function (Blueprint $table): void {
            $table->id();
            $table->string('cap_id');
            $table->string('size_id');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            $table->foreign('cap_id')->references('id')->on('caps')->cascadeOnDelete();
            $table->foreign('size_id')->references('id')->on('sizes')->cascadeOnDelete();
            $table->unique(['cap_id', 'size_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cap_size_mappings');
    }
};
