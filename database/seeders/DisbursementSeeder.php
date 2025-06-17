<?php

namespace Database\Seeders;

use App\Models\Disbursement;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DisbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have purchase invoices
        if (PurchaseInvoice::count() === 0) {
            $this->command->info('No purchase invoices found. Please run PurchaseInvoiceSeeder first.');
            return;
        }
        
        // Create disbursements for unpaid invoices
        $this->createDisbursementsForInvoices();
        
        // Create standalone disbursements
        $this->createStandaloneDisbursements();
        
        $this->command->info('Disbursements seeded successfully!');
    }
    
    /**
     * Create disbursements for unpaid invoices
     */
    private function createDisbursementsForInvoices(): void
    {
        $invoices = PurchaseInvoice::where('payment_status', '!=', 'paid')
            ->inRandomOrder()
            ->limit(10)
            ->get();
            
        $this->command->info("Creating disbursements for " . count($invoices) . " invoices...");
        
        foreach ($invoices as $invoice) {
            // Decide if this will be a full or partial payment
            $isFullPayment = fake()->boolean(70); // 70% chance of full payment
            
            $amount = $isFullPayment ? $invoice->amount_due : $invoice->amount_due * fake()->randomFloat(2, 0.3, 0.8);
            
            // Create disbursement
            $disbursement = Disbursement::factory()->create([
                'supplier_id' => $invoice->supplier_id,
                'amount' => $amount,
                'status' => 'completed',
            ]);
            
            // Attach the invoice to the disbursement with pivot data
            $disbursement->purchaseInvoices()->attach($invoice->id, [
                'amount' => $amount,
                'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            ]);
            
            // Update invoice payment amounts
            $newAmountPaid = $invoice->amount_paid + $amount;
            $newAmountDue = $invoice->total - $newAmountPaid;
            
            $paymentStatus = 'unpaid';
            if ($newAmountDue <= 0) {
                $paymentStatus = 'paid';
            } elseif ($newAmountPaid > 0) {
                $paymentStatus = 'partial';
            }
            
            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'amount_due' => $newAmountDue,
                'payment_status' => $paymentStatus,
            ]);
            
            // If this invoice is linked to a purchase order, update its payment status
            if ($invoice->purchase_order_id) {
                $this->updatePurchaseOrderPaymentStatus($invoice->purchaseOrder);
            }
        }
    }
    
    /**
     * Create standalone disbursements not linked to invoices
     */
    private function createStandaloneDisbursements(): void
    {
        $disbursementCount = 5;
        $this->command->info("Creating {$disbursementCount} standalone disbursements...");
        
        Disbursement::factory()
            ->count($disbursementCount)
            ->create();
    }
    
    /**
     * Update the payment status of a purchase order based on its invoices
     */
    private function updatePurchaseOrderPaymentStatus($purchaseOrder): void
    {
        if (!$purchaseOrder) return;
        
        // Check if all invoices are paid
        $allPaid = $purchaseOrder->invoices()->where('payment_status', '!=', 'paid')->count() === 0;
        $anyPaid = $purchaseOrder->invoices()->where('payment_status', '!=', 'unpaid')->count() > 0;
        
        if ($allPaid) {
            $purchaseOrder->update(['payment_status' => 'paid']);
        } elseif ($anyPaid) {
            $purchaseOrder->update(['payment_status' => 'partial']);
        } else {
            $purchaseOrder->update(['payment_status' => 'unpaid']);
        }
    }
}
