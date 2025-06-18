<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100+ invoices spread across 2 years
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        // Create at least 100 invoices
        $invoiceCount = 120; // A bit more than 100 to ensure we have enough
        
        // Calculate the average time interval between invoices
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerInvoice = $totalDays / $invoiceCount;
        
        for ($i = 0; $i < $invoiceCount; $i++) {
            // Calculate the invoice date with some randomness
            $invoiceDate = $startDate->copy()->addDays(ceil($i * $daysPerInvoice))
                ->addDays(rand(-3, 3)) // Add some randomness (+/- 3 days)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59)); // Random time between 8 AM and 6 PM
            
            // Ensure the date is not in the future
            if ($invoiceDate->gt($endDate)) {
                $invoiceDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            // Create the invoice with the specific date
            $invoice = Invoice::factory()->create([
                'invoice_date' => $invoiceDate,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);
            
            // For each invoice, create 1-6 invoice items
            $itemCount = rand(1, 6);
            
            // Create invoice items for this invoice
            InvoiceItem::factory()->count($itemCount)->create([
                'invoice_id' => $invoice->id
            ]);
            
            // Recalculate invoice totals based on items
            $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();
            
            $subtotal = $invoiceItems->sum('subtotal');
            $tax = $invoiceItems->sum('tax_amount');
            $discount = $invoiceItems->sum('discount_amount');
            $total = $subtotal + $tax - $discount;
            $amountPaid = $invoiceItems->sum('amount_paid');
            $amountDue = $total - $amountPaid;
            
            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'amount_paid' => $amountPaid,
                'amount_due' => $amountDue
            ]);
        }
    }
}
