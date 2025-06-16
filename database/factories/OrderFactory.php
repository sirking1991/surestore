<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $orderNumber = 1;
        
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        $shipping = fake()->randomFloat(2, 0, 200);
        
        $tax = $subtotal * $taxRate;
        $discount = $subtotal * $discountRate;
        $total = $subtotal + $tax - $discount + $shipping;
        
        $orderDate = fake()->dateTimeBetween('-2 months', 'now');
        
        return [
            'order_number' => 'ORD' . date('Y') . str_pad($orderNumber++, 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'quote_id' => fake()->optional(0.3)->randomElement(Quote::pluck('id')->toArray()),
            'order_date' => $orderDate,
            'expected_delivery_date' => fake()->dateTimeBetween($orderDate, '+30 days'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $total,
            'shipping_address' => fake()->address(),
            'billing_address' => fake()->optional(0.8)->address(),
            'notes' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_status' => fake()->randomElement(['unpaid', 'partial', 'paid']),
        ];
    }
}
