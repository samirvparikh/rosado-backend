<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Product -> Audience/Family/Occasion/Season/etc, always many-to-many. */
    public function up(): void
    {
        Schema::create('product_classifications', function (Blueprint $table): void {
            $table->id();
            $table->string('product_id');
            $table->string('classification_id');
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('classification_id')->references('id')->on('classifications')->cascadeOnDelete();
            $table->unique(['product_id', 'classification_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_classifications');
    }
};
