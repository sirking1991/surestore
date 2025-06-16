<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
        static $productNumber = 1;
        $units = ['pcs', 'kg', 'g', 'l', 'ml', 'box', 'set', 'pair', 'roll', 'sheet'];
        $categories = ['Electronics', 'Furniture', 'Clothing', 'Food', 'Beverages', 'Stationery', 'Tools', 'Appliances', 'Accessories', 'Other'];
        $brands = ['Brand A', 'Brand B', 'Brand C', 'Brand D', 'Brand E', 'Brand F', 'Generic', 'Premium', 'Economy', 'Luxury'];
        
        $purchasePrice = fake()->randomFloat(2, 10, 1000);
        $sellingPrice = $purchasePrice * fake()->randomFloat(2, 1.1, 2.0); // 10-100% markup
        
        return [
            'code' => 'PROD' . str_pad($productNumber++, 5, '0', STR_PAD_LEFT),
            'name' => fake()->words(fake()->numberBetween(2, 4), true),
            'description' => fake()->optional(0.8)->paragraph(),
            'unit' => fake()->randomElement($units),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            'stock' => fake()->randomFloat(2, 0, 1000),
            'min_stock' => fake()->randomFloat(2, 5, 50),
            'max_stock' => fake()->randomFloat(2, 100, 2000),
            'barcode' => fake()->optional(0.7)->ean13(),
            'category' => fake()->randomElement($categories),
            'brand' => fake()->randomElement($brands),
            'image' => null, // Would need actual image handling logic
            'is_active' => fake()->boolean(90),
            'is_service' => fake()->boolean(10),
        ];
    }
}
