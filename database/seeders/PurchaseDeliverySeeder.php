<?php

namespace Database\Seeders;

use App\Models\PurchaseDelivery;
use App\Models\PurchaseDeliveryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have purchase orders
        if (PurchaseOrder::count() === 0) {
            $this->command->info('No purchase orders found. Please run PurchaseOrderSeeder first.');
            return;
        }
        
        // Create some deliveries linked to purchase orders
        $linkedDeliveryCount = 10;
        $this->command->info("Creating {$linkedDeliveryCount} deliveries linked to purchase orders...");
        
        $purchaseOrders = PurchaseOrder::inRandomOrder()->limit($linkedDeliveryCount)->get();
        
        foreach ($purchaseOrders as $purchaseOrder) {
            // Create a delivery for this purchase order
            $delivery = PurchaseDelivery::factory()->create([
                'supplier_id' => $purchaseOrder->supplier_id,
                'purchase_order_id' => $purchaseOrder->id,
            ]);
            
            // For each purchase order item, create a delivery item
            foreach ($purchaseOrder->items as $orderItem) {
                // Randomly decide how much of the ordered quantity is being delivered
                $quantityRatio = fake()->randomFloat(2, 0.5, 1.0);
                $quantityDelivered = $orderItem->quantity * $quantityRatio;
                
                PurchaseDeliveryItem::factory()->create([
                    'purchase_delivery_id' => $delivery->id,
                    'product_id' => $orderItem->product_id,
                    'purchase_order_item_id' => $orderItem->id,
                    'quantity' => $quantityDelivered,
                    'quantity_received' => $quantityDelivered,
                    'unit' => $orderItem->unit,
                ]);
                
                // Update the purchase order item
                $orderItem->update([
                    'quantity_received' => $quantityDelivered,
                ]);
            }
            
            // Update purchase order delivery status based on items
            $this->updatePurchaseOrderDeliveryStatus($purchaseOrder);
        }
        
        // Create some standalone deliveries (not linked to purchase orders)
        $standaloneDeliveryCount = 5;
        $this->command->info("Creating {$standaloneDeliveryCount} standalone deliveries...");
        
        $deliveries = PurchaseDelivery::factory()
            ->count($standaloneDeliveryCount)
            ->create([
                'purchase_order_id' => null,
            ]);
            
        foreach ($deliveries as $delivery) {
            // Create between 2-5 items for each delivery
            $itemCount = rand(2, 5);
            
            PurchaseDeliveryItem::factory()
                ->count($itemCount)
                ->create([
                    'purchase_delivery_id' => $delivery->id,
                    'purchase_order_item_id' => null,
                ]);
        }
        
        $this->command->info('Purchase deliveries and items seeded successfully!');
    }
    
    /**
     * Update the delivery status of a purchase order based on its items
     */
    private function updatePurchaseOrderDeliveryStatus(PurchaseOrder $purchaseOrder): void
    {
        $items = $purchaseOrder->items;
        $totalQuantity = $items->sum('quantity');
        $totalReceived = $items->sum('quantity_received');
        
        if ($totalReceived <= 0) {
            $purchaseOrder->update(['delivery_status' => 'pending']);
        } elseif ($totalReceived >= $totalQuantity) {
            $purchaseOrder->update(['delivery_status' => 'complete']);
        } else {
            $purchaseOrder->update(['delivery_status' => 'partial']);
        }
    }
}
