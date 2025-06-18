<?php

namespace Database\Seeders;

use App\Models\PurchaseDelivery;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure we have purchase orders and deliveries
        if (PurchaseOrder::count() === 0) {
            $this->command->info('No purchase orders found. Please run PurchaseOrderSeeder first.');
            return;
        }
        
        // Create invoices for purchase orders with deliveries
        $this->createInvoicesForDeliveries();
        
        // Create invoices for purchase orders without deliveries
        $this->createInvoicesForPurchaseOrders();
        
        // Create standalone invoices
        $this->createStandaloneInvoices();
        
        $this->command->info('Purchase invoices and items seeded successfully!');
    }
    
    /**
     * Create invoices for purchase orders with deliveries
     */
    private function createInvoicesForDeliveries(): void
    {
        $deliveries = PurchaseDelivery::whereNotNull('purchase_order_id')
            ->inRandomOrder()
            ->limit(40) // Increased from 8 to generate more records
            ->get();
            
        $this->command->info("Creating invoices for " . count($deliveries) . " deliveries...");
        
        foreach ($deliveries as $delivery) {
            $purchaseOrder = $delivery->purchaseOrder;
            
            // Create an invoice for this delivery with dates from the past 2 years
            $invoiceDate = \Carbon\Carbon::now()->subYears(2)->addDays(rand(0, 730));
            $invoice = PurchaseInvoice::factory()->create([
                'invoice_date' => $invoiceDate,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
                'supplier_id' => $delivery->supplier_id,
                'purchase_order_id' => $delivery->purchase_order_id,
                'purchase_delivery_id' => $delivery->id,
            ]);
            
            // For each delivery item, create an invoice item
            foreach ($delivery->items as $deliveryItem) {
                $orderItem = $deliveryItem->purchaseOrderItem;
                
                if ($orderItem) {
                    PurchaseInvoiceItem::factory()->create([
                        'purchase_invoice_id' => $invoice->id,
                        'product_id' => $deliveryItem->product_id,
                        'purchase_order_item_id' => $orderItem->id,
                        'purchase_delivery_item_id' => $deliveryItem->id,
                        'quantity' => $deliveryItem->quantity,
                        'unit' => $deliveryItem->unit,
                        'unit_price' => $orderItem->unit_price,
                    ]);
                    
                    // Update the purchase order item
                    $orderItem->update([
                        'quantity_invoiced' => $deliveryItem->quantity,
                    ]);
                }
            }
            
            // Recalculate totals based on items
            $this->recalculateInvoiceTotals($invoice);
            
            // Update purchase order payment status
            $this->updatePurchaseOrderPaymentStatus($purchaseOrder);
        }
    }
    
    /**
     * Create invoices for purchase orders without deliveries
     */
    private function createInvoicesForPurchaseOrders(): void
    {
        // Find purchase orders without invoices
        $purchaseOrders = PurchaseOrder::whereDoesntHave('invoices')
            ->inRandomOrder()
            ->limit(40) // Increased from 5 to generate more records
            ->get();
            
        $this->command->info("Creating invoices for " . count($purchaseOrders) . " purchase orders without deliveries...");
        
        foreach ($purchaseOrders as $purchaseOrder) {
            // Create an invoice for this purchase order with dates from the past 2 years
            $invoiceDate = \Carbon\Carbon::now()->subYears(2)->addDays(rand(0, 730));
            $invoice = PurchaseInvoice::factory()->create([
                'invoice_date' => $invoiceDate,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
                'supplier_id' => $purchaseOrder->supplier_id,
                'purchase_order_id' => $purchaseOrder->id,
                'purchase_delivery_id' => null,
            ]);
            
            // For each purchase order item, create an invoice item
            foreach ($purchaseOrder->items as $orderItem) {
                // Randomly decide how much of the ordered quantity is being invoiced
                $quantityRatio = fake()->randomFloat(2, 0.7, 1.0);
                $quantityInvoiced = $orderItem->quantity * $quantityRatio;
                
                PurchaseInvoiceItem::factory()->create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $orderItem->product_id,
                    'purchase_order_item_id' => $orderItem->id,
                    'purchase_delivery_item_id' => null,
                    'quantity' => $quantityInvoiced,
                    'unit' => $orderItem->unit,
                    'unit_price' => $orderItem->unit_price,
                ]);
                
                // Update the purchase order item
                $orderItem->update([
                    'quantity_invoiced' => $quantityInvoiced,
                ]);
            }
            
            // Recalculate totals based on items
            $this->recalculateInvoiceTotals($invoice);
            
            // Update purchase order payment status
            $this->updatePurchaseOrderPaymentStatus($purchaseOrder);
        }
    }
    
    /**
     * Create standalone invoices not linked to purchase orders or deliveries
     */
    private function createStandaloneInvoices(): void
    {
        $invoiceCount = 40; // Increased from 3 to generate more records
        $this->command->info("Creating {$invoiceCount} standalone invoices...");
        
        // Create invoices with dates spread over 2 years
        $startDate = \Carbon\Carbon::now()->subYears(2);
        $endDate = \Carbon\Carbon::now();
        $totalDays = $endDate->diffInDays($startDate);
        $daysPerInvoice = $totalDays / $invoiceCount;
        
        $invoices = [];
        
        for ($i = 0; $i < $invoiceCount; $i++) {
            // Calculate invoice date with some randomness
            $invoiceDate = $startDate->copy()->addDays(ceil($i * $daysPerInvoice))
                ->addDays(rand(-3, 3))
                ->setTime(rand(8, 17), rand(0, 59), rand(0, 59));
                
            // Ensure date is not in the future
            if ($invoiceDate->gt($endDate)) {
                $invoiceDate = $endDate->copy()->subDays(rand(0, 7));
            }
            
            $invoice = PurchaseInvoice::factory()->create([
                'purchase_order_id' => null,
                'purchase_delivery_id' => null,
                'invoice_date' => $invoiceDate,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);
            
            $invoices[] = $invoice;
        }
            
        foreach ($invoices as $invoice) {
            // Create between 2-5 items for each invoice
            $itemCount = rand(2, 5);
            
            PurchaseInvoiceItem::factory()
                ->count($itemCount)
                ->create([
                    'purchase_invoice_id' => $invoice->id,
                    'purchase_order_item_id' => null,
                    'purchase_delivery_item_id' => null,
                ]);
                
            // Recalculate totals based on items
            $this->recalculateInvoiceTotals($invoice);
        }
    }
    
    /**
     * Recalculate invoice totals based on items
     */
    private function recalculateInvoiceTotals(PurchaseInvoice $invoice): void
    {
        $items = $invoice->items;
        $subtotal = $items->sum('subtotal');
        $taxAmount = $items->sum('tax_amount');
        $discountAmount = $items->sum('discount_amount');
        $total = $items->sum('total');
        $amountPaid = $items->sum('amount_paid');
        $amountDue = $total - $amountPaid;
        
        $paymentStatus = 'unpaid';
        if ($amountPaid >= $total) {
            $paymentStatus = 'paid';
        } elseif ($amountPaid > 0) {
            $paymentStatus = 'partial';
        }
        
        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'payment_status' => $paymentStatus,
        ]);
    }
    
    /**
     * Update the payment status of a purchase order based on its items
     */
    private function updatePurchaseOrderPaymentStatus(PurchaseOrder $purchaseOrder): void
    {
        $items = $purchaseOrder->items;
        $totalQuantity = $items->sum('quantity');
        $totalInvoiced = $items->sum('quantity_invoiced');
        
        if ($totalInvoiced <= 0) {
            $purchaseOrder->update(['payment_status' => 'unpaid']);
        } elseif ($totalInvoiced >= $totalQuantity) {
            // Check if all invoices are paid
            $allPaid = $purchaseOrder->invoices()->where('payment_status', '!=', 'paid')->count() === 0;
            $purchaseOrder->update(['payment_status' => $allPaid ? 'paid' : 'partial']);
        } else {
            $purchaseOrder->update(['payment_status' => 'partial']);
        }
    }
}
