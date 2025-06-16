<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $customerNumber = 1;
        
        return [
            'code' => 'CUST' . str_pad($customerNumber++, 5, '0', STR_PAD_LEFT),
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
            'notes' => fake()->optional(0.7)->paragraph(),
            'credit_limit' => fake()->randomFloat(2, 1000, 50000),
            'balance' => fake()->randomFloat(2, 0, 10000),
            'is_active' => fake()->boolean(90),
        ];
    }
}
