<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = fake()->randomFloat(2, 1, 20);
        $unitPrice = $product->selling_price * fake()->randomFloat(2, 0.9, 1.2); // Some price variation
        
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $subtotal * $discountRate;
        $total = $subtotal + $taxAmount - $discountAmount;
        
        $amountPaid = fake()->randomFloat(2, 0, $total);
        $amountDue = $total - $amountPaid;
        
        return [
            'invoice_id' => Invoice::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'order_item_id' => fake()->optional(0.7)->randomElement(OrderItem::pluck('id')->toArray()),
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
