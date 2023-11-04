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
        Schema::table('stores', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'email',
                'facebook',
                'instagram',
                'linkedin',
                'twitter',
                'tiktok',
            ]);
        });
    }
};
