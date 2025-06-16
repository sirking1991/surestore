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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->date('delivery_date');
            $table->date('scheduled_date')->nullable();
            $table->text('shipping_address');
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'returned', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
