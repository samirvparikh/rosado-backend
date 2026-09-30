<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One table backs every classification master (Audience, Fragrance Family,
     * Occasion, Season, Time of Day, Intensity, Longevity, Scent Character,
     * Collection). Each is still its own set of IDs/relations via the `group`
     * column and the product_classifications pivot -- never a free-text field
     * on Product -- which is what the spec's "no arbitrary free text" rule
     * actually guards against.
     */
    public function up(): void
    {
        Schema::create('classifications', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->enum('group', [
                'AUDIENCE',
                'FRAGRANCE_FAMILY',
                'OCCASION',
                'SEASON',
                'TIME_OF_DAY',
                'INTENSITY',
                'LONGEVITY',
                'SCENT_CHARACTER',
                'COLLECTION',
            ]);
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            $table->unique(['group', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
