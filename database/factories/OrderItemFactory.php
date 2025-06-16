<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\QuoteItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
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
        
        $quantityShipped = fake()->randomFloat(2, 0, $quantity);
        $quantityInvoiced = fake()->randomFloat(2, 0, $quantity);
        
        return [
            'order_id' => Order::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'quote_item_id' => fake()->optional(0.3)->randomElement(QuoteItem::pluck('id')->toArray()),
            'description' => fake()->optional(0.3)->sentence(),
            'quantity' => $quantity,
            'quantity_shipped' => $quantityShipped,
            'quantity_invoiced' => $quantityInvoiced,
            'unit' => $product->unit,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount_rate' => $discountRate,
            'discount_amount' => $discountAmount,
            'subtotal' => $subtotal,
            'total' => $total,
            'expected_delivery_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'sort_order' => fake()->numberBetween(1, 10),
            'notes' => fake()->optional(0.2)->sentence(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
        ];
    }
}
