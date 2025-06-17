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
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_adjustments', 'reference_number')) {
                $table->string('reference_number')->unique();
            }
            if (!Schema::hasColumn('inventory_adjustments', 'adjustment_date')) {
                $table->date('adjustment_date');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'storage_location_id')) {
                $table->foreignId('storage_location_id')->constrained('storage_locations');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'adjustment_type')) {
                $table->string('adjustment_type')->comment('addition, subtraction, transfer');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('inventory_adjustments', 'status')) {
                $table->string('status')->default('draft')->comment('draft, approved, cancelled');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users');
            }
            if (!Schema::hasColumn('inventory_adjustments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $columns = ['reference_number', 'adjustment_date', 'storage_location_id', 'adjustment_type', 'notes', 'status', 'created_by', 'approved_by', 'approved_at'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('inventory_adjustments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
