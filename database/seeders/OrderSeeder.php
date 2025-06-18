<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 100+ orders spread across 2 years
        $startDate = Carbon::now()->subYears(2);
        $endDate = Carbon::now();
        
        // Create at least 100 orders
        $orderCount = 120; // A bit more than 100 to ensure we have enough
        
        // Calculate the average time interval between orders
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerOrder = $totalDays / $orderCount;
        
        for ($i = 0; $i < $orderCount; $i++) {
            // Calculate the order date with some randomness
            $orderDate = $startDate->copy()->addDays(ceil($i * $daysPerOrder))
                ->addDays(rand(-3, 3)) // Add some randomness (+/- 3 days)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59)); // Random time between 8 AM and 6 PM
            
            // Ensure the date is not in the future
            if ($orderDate->gt($endDate)) {
                $orderDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            // Create the order with the specific date
            $order = Order::factory()->create([
                'order_date' => $orderDate,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
            
            // For each order, create 1-6 order items
            $itemCount = rand(1, 6);
            
            // Create order items for this order
            OrderItem::factory()->count($itemCount)->create([
                'order_id' => $order->id
            ]);
            
            // Recalculate order totals based on items
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            
            $subtotal = $orderItems->sum('subtotal');
            $tax = $orderItems->sum('tax_amount');
            $discount = $orderItems->sum('discount_amount');
            $total = $subtotal + $tax - $discount + $order->shipping;
            
            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total
            ]);
        }
    }
}
