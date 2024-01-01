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
                ['value' => 'red', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'green', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'blue', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'cyan', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'magenta', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'yellow', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'black', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
            ],
            'sizes' => [
                ['value' => 'small', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'medium', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
                ['value' => 'large', 'addon_price' => round(fake()->randomFloat(0, 1000)/100,2)],
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
