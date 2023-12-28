<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word();
        $imgOptions = [
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/category_img_01.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/category_img_02.jpg',
            'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/category_img_03.jpg',
        ];
        return [
            'name' => $name,
            'slug'=> Str::slug($name),
            'image_url' => $imgOptions[array_rand($imgOptions)]
        ];
    }
}
