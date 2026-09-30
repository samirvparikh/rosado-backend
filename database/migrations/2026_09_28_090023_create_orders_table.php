<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['PLACED', 'CONFIRMED', 'SHIPPED', 'DELIVERED', 'CANCELLED'])->default('PLACED');

            // Customer/shipping details are captured at order time, independent
            // of any saved Address row, so the order never changes retroactively.
            $table->string('customer_full_name');
            $table->string('customer_mobile', 20);
            $table->string('customer_email');
            $table->string('customer_address');
            $table->string('customer_city');
            $table->string('customer_state');
            $table->string('customer_pincode', 12);

            $table->string('shipping_method_id');
            $table->string('shipping_method_label');
            $table->enum('payment_method', ['COD', 'UPI', 'CARD']);
            $table->string('coupon_code')->nullable();

            $table->decimal('subtotal', 10, 2)->unsigned();
            $table->decimal('discount', 10, 2)->unsigned()->default(0);
            $table->decimal('tax', 10, 2)->unsigned()->default(0);
            $table->decimal('shipping', 10, 2)->unsigned()->default(0);
            $table->decimal('final_price', 10, 2)->unsigned();

            $table->timestamps();

            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
