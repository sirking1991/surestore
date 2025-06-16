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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->decimal('quantity_invoiced', 15, 2)->default(0);
            $table->string('unit');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_rate', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('expected_delivery_date')->nullable();
            $table->integer('sort_order')->default(1);
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
