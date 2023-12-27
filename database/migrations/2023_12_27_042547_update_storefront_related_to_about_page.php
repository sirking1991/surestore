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
        Schema::table('store_fronts', function (Blueprint $table) {
            $table->text('meta_about');
            $table->dropColumn('about');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_fronts', function (Blueprint $table) {
            $table->dropColumn('meta_about');
            $table->text('meta_about');
        });
    }
};
