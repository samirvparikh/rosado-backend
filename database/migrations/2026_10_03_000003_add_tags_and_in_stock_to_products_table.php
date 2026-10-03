<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `tags` are free-form search keywords per perfume. `in_stock` is the
     * admin's manual availability switch: off = Sold Out on the storefront
     * regardless of per-size stock. ("Online" visibility is the existing
     * `status` column: ACTIVE = shown on web.)
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->json('tags')->nullable()->after('brand');
            $table->boolean('in_stock')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['tags', 'in_stock']);
        });
    }
};
