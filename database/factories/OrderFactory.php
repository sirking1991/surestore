<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        $address = [
            'street' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'post_code' => fake()->postcode(),
            'phone_number' => fake()->phoneNumber(),
        ];
        $status = fake()->randomElement(['draft', 'open', 'paid', 'for-shipping', 'refunded', 'shipped', 'returned']);
        return [
            'number' => fake()->randomNumber(8),
            'status' => $status,
            'order_date' => \Carbon\Carbon::now()->sub('day', rand(0,90)),
            'customer_id' => User::inRandomOrder()->first()->id,
            'shipping_address' => json_encode($address),
            'billing_address' => json_encode($address),
        ];
    }
}
