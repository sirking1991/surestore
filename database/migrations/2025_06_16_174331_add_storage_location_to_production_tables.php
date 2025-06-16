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
        Schema::table('production_materials', function (Blueprint $table) {
            $table->foreignId('storage_location_id')->nullable()->after('storage_id')->constrained()->nullOnDelete();
        });

        Schema::table('production_products', function (Blueprint $table) {
            $table->foreignId('storage_location_id')->nullable()->after('storage_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_materials', function (Blueprint $table) {
            $table->dropForeign(['storage_location_id']);
            $table->dropColumn('storage_location_id');
        });

        Schema::table('production_products', function (Blueprint $table) {
            $table->dropForeign(['storage_location_id']);
            $table->dropColumn('storage_location_id');
        });
    }
};
