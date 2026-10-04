<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Secret per-order key so a guest (or a customer whose session expired) can
 * open their own order confirmation. Order IDs are sequential, so the ID
 * alone must never be enough to read an order.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('access_token', 64)->nullable()->after('order_number');
        });

        DB::table('orders')->whereNull('access_token')->orderBy('id')->each(function ($order): void {
            DB::table('orders')->where('id', $order->id)->update(['access_token' => Str::random(40)]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('access_token');
        });
    }
};
