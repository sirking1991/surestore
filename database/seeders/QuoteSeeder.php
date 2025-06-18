<?php

namespace Database\Seeders;

use App\Models\Quote;
use App\Models\QuoteItem;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100+ quotes spread across 2 years
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        // Create at least 100 quotes
        $quoteCount = 120; // A bit more than 100 to ensure we have enough
        
        // Calculate the average time interval between quotes
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerQuote = $totalDays / $quoteCount;
        
        for ($i = 0; $i < $quoteCount; $i++) {
            // Calculate the quote date with some randomness
            $quoteDate = $startDate->copy()->addDays(ceil($i * $daysPerQuote))
                ->addDays(rand(-3, 3)) // Add some randomness (+/- 3 days)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59)); // Random time between 8 AM and 6 PM
            
            // Ensure the date is not in the future
            if ($quoteDate->gt($endDate)) {
                $quoteDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            // Create the quote with the specific date
            $quote = Quote::factory()->create([
                'quote_date' => $quoteDate,
                'created_at' => $quoteDate,
                'updated_at' => $quoteDate,
            ]);
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
        }
    }
}
