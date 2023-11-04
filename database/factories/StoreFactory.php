<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'about' => fake()->paragraphs(asText:true),
            'meta' => json_encode([]),
            'creator_id' => User::inRandomOrder()->first()->id,
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'street' => fake()->streetAddress,
            'city' => fake()->city,
            'state' => fake()->stateAbbr,
            'country' => fake()->country,
            'postal_code' => fake()->postcode,
            'facebook' => 'facebook.com/' . Str::slug($name),
            'instagram' => 'instagram.com/' . Str::slug($name),
            'tiktok' => 'tiktok.com/' . Str::slug($name),
        ];
    }
}
