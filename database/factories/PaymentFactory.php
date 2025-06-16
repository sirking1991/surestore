<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $paymentNumber = 1;
        
        $paymentMethods = ['cash', 'check', 'bank_transfer', 'credit_card', 'debit_card', 'online_payment', 'other'];
        
        return [
            'payment_number' => 'PAY' . date('Y') . str_pad($paymentNumber++, 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'invoice_id' => fake()->optional(0.8)->randomElement(Invoice::pluck('id')->toArray()),
            'payment_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'payment_method' => fake()->randomElement($paymentMethods),
            'reference_number' => fake()->optional(0.7)->bothify('REF-####-????-####'),
            'notes' => fake()->optional(0.5)->paragraph(),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'refunded']),
        ];
    }
}
