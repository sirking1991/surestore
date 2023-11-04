<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')
                ->nullable()
                ->index()
                ->comment('if null, order is draft');
            $table->string('status')
                ->default('draft')
                ->comment('draft -> open -> paid -> for-shipping|refunded -> shipped|returned -> refunded');
            $table->unsignedBigInteger('store_id')->index();
            $table->dateTime('order_date')->index();
            $table->unsignedBigInteger('customer_id')
                ->nullable()
                ->index()
                ->comment('if null, order is draft');
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();
            $table->double('total_item_amount')->default(0);
            $table->double('discount')->default(0);
            $table->json('discount_detail')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
