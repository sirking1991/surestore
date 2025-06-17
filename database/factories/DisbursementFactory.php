<?php

namespace Database\Factories;

use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Disbursement>
 */
class DisbursementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplier = Supplier::inRandomOrder()->first();
        $amount = fake()->randomFloat(2, 100, 5000);
        
        $disbursementDate = fake()->dateTimeBetween('-1 month', 'now');
        
        $paymentMethods = ['cash', 'bank_transfer', 'check', 'credit_card', 'debit_card', 'online_payment'];
        $statuses = ['pending', 'completed', 'cancelled', 'failed'];
        
        return [
            'code' => 'DIS-' . fake()->unique()->numerify('######'),
            'supplier_id' => $supplier->id,
            'reference_number' => fake()->optional(0.8)->bothify('REF-????-####'),
            'disbursement_date' => $disbursementDate,
            'amount' => $amount,
            'payment_method' => fake()->randomElement($paymentMethods),
            'bank_account' => fake()->optional(0.5)->numerify('################'),
            'check_number' => fake()->optional(0.3)->numerify('########'),
            'transaction_id' => fake()->optional(0.6)->uuid(),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
