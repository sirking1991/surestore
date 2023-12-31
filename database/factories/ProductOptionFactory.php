<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductOption>
 */
class ProductOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $availableOptions = [
            'colors' => [
                ['value' => 'red', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'green', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'blue', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'cyan', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'magenta', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'yellow', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'black', 'addon_price' => fake()->randomFloat(0, 1000)],
            ],
            'sizes' => [
                ['value' => 'small', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'medium', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['value' => 'large', 'addon_price' => fake()->randomFloat(0, 1000)],
            ],
        ];
        $o = array_rand($availableOptions);
        $option = $availableOptions[$o];
        
        return [
            'name' => $o,
            'options' => $availableOptions[$o]
        ];
    }
}
