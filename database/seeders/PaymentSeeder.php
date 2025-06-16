<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create payments
        Payment::factory()->count(25)->create()->each(function ($payment) {
            // For each payment, create 1-3 payment items
            $itemCount = rand(1, 3);
            
            // Create payment items for this payment
            PaymentItem::factory()->count($itemCount)->create([
                'payment_id' => $payment->id
            ]);
            
            // Recalculate payment amount based on items
            $paymentItems = PaymentItem::where('payment_id', $payment->id)->get();
            $totalAmount = $paymentItems->sum('amount');
            
            $payment->update([
                'amount' => $totalAmount
            ]);
            
            // Update invoice paid amounts if this payment has an invoice
            if ($payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice) {
                    $invoicePayments = Payment::where('invoice_id', $invoice->id)->get();
                    $totalPaid = $invoicePayments->sum('amount');
                    
                    $invoice->update([
                        'amount_paid' => $totalPaid,
                        'amount_due' => $invoice->total - $totalPaid,
                        'status' => $totalPaid >= $invoice->total ? 'paid' : 
                                   ($totalPaid > 0 ? 'partial' : $invoice->status)
                    ]);
                }
            }
        });
    }
}
