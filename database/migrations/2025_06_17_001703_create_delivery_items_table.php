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
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items');
            $table->foreignId('storage_id')->nullable()->constrained('storages');
            $table->foreignId('storage_location_id')->nullable()->constrained('storage_locations');
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('quantity_received', 15, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('weight', 15, 2)->nullable();
            $table->string('weight_unit')->default('kg')->nullable();
            $table->decimal('volume', 15, 2)->nullable();
            $table->string('volume_unit')->default('m3')->nullable();
            $table->string('package_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'shipped', 'delivered', 'returned', 'damaged'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            
            // Add individual indexes for faster lookups
            $table->index('product_id');
            $table->index('storage_id');
            $table->index('storage_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
