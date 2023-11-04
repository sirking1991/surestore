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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('option_id')
                ->nullable()
                ->index();
            $table->unsignedBigInteger('option_value')
                ->nullable();
            $table->double('base_price')->default(0);
            $table->double('addon_price')->default(0);
            $table->double('price')
                ->virtualAs('base_price + addon_price');
            $table->double('discount')->default(0);
            $table->json('discount_detail')->nullable();
            $table->double('amount')
                ->virtualAs('price - discount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
