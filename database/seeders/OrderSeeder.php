<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create orders
        Order::factory()->count(20)->create()->each(function ($order) {
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
        });
    }
}
