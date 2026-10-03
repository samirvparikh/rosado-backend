<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Optional customer remarks on a custom perfume line, frozen with the order snapshot. */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->text('remarks')->nullable()->after('cap_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn('remarks');
        });
    }
};
