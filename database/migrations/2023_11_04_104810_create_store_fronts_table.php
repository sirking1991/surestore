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
        Schema::create('store_fronts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('status')
                ->default('active')
                ->comment('active, draft');
            $table->text('about');

            $table->string('phone', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('facebook', 50)->nullable();
            $table->string('instagram', 50)->nullable();
            $table->string('linkedin', 50)->nullable();
            $table->string('twitter', 50)->nullable();
            $table->string('tiktok', 50)->nullable();

            $table->json('contact_details')->nullable();
            $table->json('banner_products')->nullable();
            $table->json('month_category')->nullable();
            $table->json('featured_products')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_fronts');
    }
};
