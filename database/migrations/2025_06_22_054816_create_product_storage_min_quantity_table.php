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
        Schema::create('product_storage_min_quantity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            $table->foreignId('storage_location_id')
                ->constrained('storage_locations')
                ->cascadeOnDelete();
            $table->decimal('min_quantity', 15, 2)->default(0);
            $table->timestamps();
            
            // Ensure we don't have duplicate entries for product + location
            $table->unique(
                ['product_id', 'storage_location_id'],
                'prod_storage_min_qty_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_storage_min_quantity');
    }
};
