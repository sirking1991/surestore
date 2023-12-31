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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->string('description')->index();
            $table->string('sku')->index();
            $table->unsignedBigInteger('parent_product_id')
                ->nullable()
                ->index();
            $table->boolean('available_for_sale')->default(false);
            $table->string('uom')->default('each');
            $table->double('price');
            $table->json('images');
            $table->double('rating',1)->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
