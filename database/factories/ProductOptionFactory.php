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
        $options = [
            'colors' => [
                fake()->randomElements(
                    count:rand(1,5), 
                    array:[
                        ['name' => 'red', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'green', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'blue', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'cyan', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'magenta', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'yellow', 'addon_price' => fake()->randomFloat(0, 1000)],
                        ['name' => 'black', 'addon_price' => fake()->randomFloat(0, 1000)],
                    ]
                )
            ],
            'sizes' => [
                ['name' => 'small', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['name' => 'medium', 'addon_price' => fake()->randomFloat(0, 1000)],
                ['name' => 'large', 'addon_price' => fake()->randomFloat(0, 1000)],
            ],
        ];
        $o = array_rand($options);
        $option = $options[$o];
        
        return [
            'name' => $o,
            'options' =>json_encode($option)
        ];
    }
}
