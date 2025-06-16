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
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('quote_item_id')->nullable()->constrained('quote_items');
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_shipped', 15, 2)->default(0);
            $table->decimal('quantity_invoiced', 15, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 8, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('expected_delivery_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
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
