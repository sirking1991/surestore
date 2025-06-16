<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create quotes
        Quote::factory()->count(15)->create()->each(function ($quote) {
            // For each quote, create 1-5 quote items
            $itemCount = rand(1, 5);
            
            // Create quote items for this quote
            QuoteItem::factory()->count($itemCount)->create([
                'quote_id' => $quote->id
            ]);
            
            // Recalculate quote totals based on items
            $quoteItems = QuoteItem::where('quote_id', $quote->id)->get();
            
            $subtotal = $quoteItems->sum('subtotal');
            $tax = $quoteItems->sum('tax_amount');
            $discount = $quoteItems->sum('discount_amount');
            $total = $subtotal + $tax - $discount;
            
            $quote->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total
            ]);
        });
    }
}
