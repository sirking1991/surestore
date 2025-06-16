<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create invoices
        Invoice::factory()->count(18)->create()->each(function ($invoice) {
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
        });
    }
}
