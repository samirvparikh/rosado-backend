<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The two personalised text lines printed on a custom perfume's bottle label. */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->string('label_line1', 30)->nullable()->after('remarks');
            $table->string('label_line2', 30)->nullable()->after('label_line1');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['label_line1', 'label_line2']);
        });
    }
};
