<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence();
        $images = [];
        for ($i = 0; $i < rand(3,10); $i++) {
            $images[] = fake()->imageUrl(format:'jpg');
        }
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentences(asText:true),
            'images' => json_encode($images),
            'sku' => strtoupper(Str::random()),
            'price' => fake()->randomFloat(nbMaxDecimals:2, min: 10, max: 20000),
            'available_for_sale' => rand(0,1) == 1,            
        ];
    }
}
