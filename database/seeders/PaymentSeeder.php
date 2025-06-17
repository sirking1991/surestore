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
        // Get the current count of payments to ensure unique payment numbers
        $paymentCount = Payment::count();
        
        // Create payments with unique payment numbers
        Payment::factory()->count(5)->make()->each(function ($payment) use (&$paymentCount) {
            // Generate a unique payment number
            $paymentCount++;
            $date = now()->format('Ymd');
            $payment->payment_number = 'PAY-' . $date . '-' . str_pad($paymentCount, 4, '0', STR_PAD_LEFT);
            $payment->save();
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
                    
                    $newStatus = $invoice->status; // Keep current status as default
                    
                    if ($totalPaid >= $invoice->total) {
                        $newStatus = 'paid';
                    } elseif ($totalPaid > 0) {
                        // If partially paid but not fully, mark as 'sent' instead of 'partial'
                        $newStatus = 'sent';
                    }
                    
                    $invoice->update([
                        'amount_paid' => $totalPaid,
                        'amount_due' => $invoice->total - $totalPaid,
                        'status' => $newStatus
                    ]);
                }
            }
        });
    }
}
