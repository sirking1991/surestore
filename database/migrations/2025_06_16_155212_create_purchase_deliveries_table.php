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
        Schema::create('purchase_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders');
            $table->date('delivery_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->decimal('total_weight', 15, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->decimal('total_volume', 15, 2)->nullable();
            $table->string('volume_unit')->nullable();
            $table->string('status')->default('pending'); // pending, in-transit, delivered, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_deliveries');
    }
};
