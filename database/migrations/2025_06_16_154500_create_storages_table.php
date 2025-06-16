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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('manager')->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->string('capacity_unit')->default('sqm');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('zone')->nullable();
            $table->string('aisle')->nullable();
            $table->string('rack')->nullable();
            $table->string('shelf')->nullable();
            $table->string('bin')->nullable();
            $table->text('description')->nullable();
            $table->decimal('capacity', 15, 2)->nullable();
            $table->string('capacity_unit')->default('sqm');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Create individual indexes for faster lookups
            $table->index('storage_id');
            $table->index('zone');
            $table->index('aisle');
            $table->index('rack');
            $table->index('shelf');
            $table->index('bin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
        Schema::dropIfExists('storages');
    }
};
