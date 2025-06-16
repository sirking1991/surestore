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
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('purchase_invoice_id')->nullable()->constrained('purchase_invoices');
            $table->string('reference_number')->nullable();
            $table->date('disbursement_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank_transfer, check, credit_card, etc.
            $table->string('bank_account')->nullable();
            $table->string('check_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('completed'); // pending, completed, cancelled, failed
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
        Schema::dropIfExists('disbursements');
    }
};
