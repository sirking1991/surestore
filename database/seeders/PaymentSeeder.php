<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Invoice;
use Carbon\Carbon;
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
        
        // Create 100+ payments spread across 2 years
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        // Create at least 100 payments
        $paymentTotal = 120; // A bit more than 100 to ensure we have enough
        
        // Calculate the average time interval between payments
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerPayment = $totalDays / $paymentTotal;
        
        for ($i = 0; $i < $paymentTotal; $i++) {
            // Calculate the payment date with some randomness
            $paymentDate = $startDate->copy()->addDays(ceil($i * $daysPerPayment))
                ->addDays(rand(-3, 3)) // Add some randomness (+/- 3 days)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59)); // Random time between 8 AM and 6 PM
            
            // Ensure the date is not in the future
            if ($paymentDate->gt($endDate)) {
                $paymentDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            // Generate a unique payment number
            $paymentCount++;
            $date = $paymentDate->format('Ymd');
            $paymentNumber = 'PAY-' . $date . '-' . str_pad($paymentCount, 4, '0', STR_PAD_LEFT);
            
            // Create the payment with the specific date
            $payment = Payment::factory()->make([
                'payment_date' => $paymentDate,
                'payment_number' => $paymentNumber,
                'created_at' => $paymentDate,
                'updated_at' => $paymentDate,
            ]);
            
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
        }
    }
}
