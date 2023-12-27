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
            'meta_about' => json_encode(
                [
                    'text' => fake()->paragraphs(asText: true),
                    'image' => 'https://templatemo.com/templates/templatemo_559_zay_shop/assets/img/about-hero.svg',
                    'our_services' => [
                        'text' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod Lorem ipsum dolor sit amet.</p>',
                        'services' => [
                            ['icon'=>'fa-truck', 'text'=>'Delivery Services'],
                            ['icon'=>'fa-exchange-alt', 'text'=>'Shipping & Return'],
                            ['icon'=>'fa-percent', 'text'=>'Promotion'],
                            ['icon'=>'fa-user', 'text'=>'24 Hours Service'],
                        ]
                    ]
                ]
            ),
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
