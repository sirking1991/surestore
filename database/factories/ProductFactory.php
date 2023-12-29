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
        $imgOptions = [
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_01.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_02.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_03.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_04.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_05.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_06.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_07.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_08.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/shop_09.jpg',
        ];
        for ($i = 0; $i < rand(3,10); $i++) {
            $images[] = $imgOptions[array_rand($imgOptions)];
        }
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentences(asText:true),
            'images' => $images,
            'sku' => strtoupper(Str::random()),
            'price' => fake()->randomFloat(nbMaxDecimals:2, min: 10, max: 20000),
            'available_for_sale' => rand(0,1) == 1,   
            'rating' => rand(1,5),
        ];
    }
}
