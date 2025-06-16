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
        Schema::create('purchase_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_delivery_id')->constrained('purchase_deliveries')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items');
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->string('unit');
            $table->decimal('weight', 15, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->decimal('volume', 15, 2)->nullable();
            $table->string('volume_unit')->nullable();
            $table->string('package_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('lot_number')->nullable();
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
        Schema::dropIfExists('purchase_delivery_items');
    }
};
