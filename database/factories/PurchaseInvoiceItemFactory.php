<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseDeliveryItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseInvoiceItem>
 */
class PurchaseInvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $purchaseInvoice = PurchaseInvoice::inRandomOrder()->first();
        
        $purchaseOrderItem = null;
        $purchaseDeliveryItem = null;
        
        if ($purchaseInvoice->purchase_order_id) {
            $purchaseOrderItem = PurchaseOrderItem::where('purchase_order_id', $purchaseInvoice->purchase_order_id)
                ->where('product_id', $product->id)
                ->inRandomOrder()
                ->first();
        }
        
        if ($purchaseInvoice->purchase_delivery_id) {
            $purchaseDeliveryItem = PurchaseDeliveryItem::where('purchase_delivery_id', $purchaseInvoice->purchase_delivery_id)
                ->where('product_id', $product->id)
                ->inRandomOrder()
                ->first();
        }
        
        $quantity = fake()->randomFloat(2, 1, 20);
        $unitPrice = $product->cost_price * fake()->randomFloat(2, 0.9, 1.1); // Some price variation
        
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $subtotal * $discountRate;
        $total = $subtotal + $taxAmount - $discountAmount;
        
        $amountPaid = fake()->randomFloat(2, 0, $total);
        $amountDue = $total - $amountPaid;
        
        return [
            'purchase_invoice_id' => $purchaseInvoice->id,
            'product_id' => $product->id,
            'purchase_order_item_id' => $purchaseOrderItem ? $purchaseOrderItem->id : null,
            'purchase_delivery_item_id' => $purchaseDeliveryItem ? $purchaseDeliveryItem->id : null,
            'description' => fake()->optional(0.3)->sentence(),
            'quantity' => $quantity,
            'unit' => $product->unit,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_rate' => $discountRate,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'sort_order' => fake()->numberBetween(1, 10),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }
}
