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
        
        // Create purchase orders
        $purchaseOrderCount = 15;
        $this->command->info("Creating {$purchaseOrderCount} purchase orders...");
        
        $purchaseOrders = PurchaseOrder::factory()
            ->count($purchaseOrderCount)
            ->create();
            
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
