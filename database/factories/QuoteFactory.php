<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $quoteNumber = 1;
        
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $tax = $subtotal * $taxRate;
        $discount = $subtotal * $discountRate;
        $total = $subtotal + $tax - $discount;
        
        return [
            'quote_number' => 'QT' . date('Y') . str_pad($quoteNumber++, 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'quote_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'valid_until' => fake()->dateTimeBetween('now', '+2 months'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'notes' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['draft', 'sent', 'accepted', 'rejected', 'expired']),
        ];
    }
}
