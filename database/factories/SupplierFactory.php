<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $supplierNumber = 1;
        
        return [
            'code' => 'SUPP' . str_pad($supplierNumber++, 5, '0', STR_PAD_LEFT),
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'alternative_phone' => fake()->optional(0.3)->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'tax_id' => fake()->optional(0.8)->numerify('###-###-###'),
            'bank_name' => fake()->optional(0.7)->company(),
            'bank_account' => fake()->optional(0.7)->numerify('##########'),
            'bank_account_name' => fake()->optional(0.7)->company(),
            'notes' => fake()->optional(0.6)->paragraph(),
            'credit_limit' => fake()->randomFloat(2, 5000, 100000),
            'balance' => fake()->randomFloat(2, 0, 20000),
            'is_active' => fake()->boolean(90),
        ];
    }
}
