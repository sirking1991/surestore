<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StoreFront>
 */
class StoreFrontFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'about' => fake()->paragraphs(asText: true),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'street' => fake()->streetAddress,
            'city' => fake()->city,
            'state' => fake()->stateAbbr,
            'country' => fake()->country,
            'postal_code' => fake()->postcode,
            'facebook' => 'https:://facebook.com/',
            'instagram' => 'https:://instagram.com/',
            'tiktok' => 'https:://tiktok.com/',
            'month_category' => '[]',
        ];
    }
}
