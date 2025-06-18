<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have suppliers
        if (Supplier::count() === 0) {
            $this->command->info('No suppliers found. Please run SupplierSeeder first.');
            return;
        }
        
        // Create purchase orders spread across 2 years
        $startDate = \Carbon\Carbon::now()->subYears(2);
        $endDate = \Carbon\Carbon::now();
        
        // Create at least 100 purchase orders
        $purchaseOrderCount = 120; // A bit more than 100 to ensure we have enough
        $this->command->info("Creating {$purchaseOrderCount} purchase orders...");
        
        // Calculate the average time interval between purchase orders
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerPO = $totalDays / $purchaseOrderCount;
        
        $purchaseOrders = [];
        
        for ($i = 0; $i < $purchaseOrderCount; $i++) {
            // Calculate the purchase order date with some randomness
            $poDate = $startDate->copy()->addDays(ceil($i * $daysPerPO))
                ->addDays(rand(-3, 3)) // Add some randomness (+/- 3 days)
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59)); // Random time between 8 AM and 6 PM
            
            // Ensure the date is not in the future
            if ($poDate->gt($endDate)) {
                $poDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            // Create the purchase order with the specific date
            $purchaseOrder = PurchaseOrder::factory()->create([
                'order_date' => $poDate,
                'created_at' => $poDate,
                'updated_at' => $poDate,
            ]);
            
            $purchaseOrders[] = $purchaseOrder;
        }
            
        $this->command->info('Creating purchase order items...');
        
        // For each purchase order, create between 2-8 items
        foreach ($purchaseOrders as $purchaseOrder) {
            $itemCount = rand(2, 8);
            
            // Create items for this purchase order
            PurchaseOrderItem::factory()
                ->count($itemCount)
                ->create([
                    'purchase_order_id' => $purchaseOrder->id,
                ]);
                
            // Recalculate totals based on items
            $items = $purchaseOrder->items;
            $subtotal = $items->sum('subtotal');
            $taxAmount = $items->sum('tax_amount');
            $discountAmount = $items->sum('discount_amount');
            $total = $subtotal + $taxAmount - $discountAmount;
            
            // Update the purchase order with calculated totals
            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
            ]);
        }
        
        $this->command->info('Purchase orders and items seeded successfully!');
    }
}
