<?php

namespace Database\Factories;

use App\Models\PurchaseDelivery;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseInvoice>
 */
class PurchaseInvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchaseOrder = PurchaseOrder::inRandomOrder()->first();
        $purchaseDelivery = null;
        $supplier = null;
        
        if ($purchaseOrder) {
            $supplier = $purchaseOrder->supplier;
            $purchaseDelivery = PurchaseDelivery::where('purchase_order_id', $purchaseOrder->id)
                ->inRandomOrder()
                ->first();
        } else {
            $supplier = Supplier::inRandomOrder()->first();
        }
        
        $invoiceDate = fake()->dateTimeBetween('-2 months', 'now');
        $dueDate = fake()->dateTimeBetween($invoiceDate, '+30 days');
        
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $subtotal * $discountRate;
        $shippingCost = fake()->randomFloat(2, 0, 200);
        $otherCharges = fake()->randomFloat(2, 0, 100);
        
        $total = $subtotal + $taxAmount - $discountAmount + $shippingCost + $otherCharges;
        $amountPaid = fake()->randomFloat(2, 0, $total);
        $amountDue = $total - $amountPaid;
        
        $paymentStatus = 'unpaid';
        if ($amountPaid >= $total) {
            $paymentStatus = 'paid';
        } elseif ($amountPaid > 0) {
            $paymentStatus = 'partial';
        }
        
        $statuses = ['draft', 'issued', 'cancelled'];
        
        return [
            'code' => 'PI-' . fake()->unique()->numerify('######'),
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $purchaseOrder ? $purchaseOrder->id : null,
            'purchase_delivery_id' => $purchaseDelivery ? $purchaseDelivery->id : null,
            'supplier_invoice_number' => fake()->optional(0.8)->bothify('INV-????-####'),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'shipping_cost' => $shippingCost,
            'other_charges' => $otherCharges,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'payment_status' => $paymentStatus,
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
