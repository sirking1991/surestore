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
        Schema::table('product_storage_min_quantity', function (Blueprint $table) {
            $table->foreignId('storage_id')->after('product_id')->nullable()->constrained('storages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_storage_min_quantity', function (Blueprint $table) {
            $table->dropForeign(['storage_id']);
            $table->dropColumn('storage_id');
        });
    }
};
