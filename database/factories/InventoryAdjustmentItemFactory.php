<?php

namespace Database\Factories;

use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\InventoryAdjustmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryAdjustmentItem>
 */
class InventoryAdjustmentItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random product ID
        $productId = Product::inRandomOrder()->first()?->id ?? 1;
        $product = Product::find($productId);
        
        // Default unit from product if available
        $unit = $product?->unit ?? 'pcs';
        
        return [
            'inventory_adjustment_id' => InventoryAdjustment::factory(),
            'product_id' => $productId,
            'quantity' => $this->faker->randomFloat(2, 1, 100),
            'unit_cost' => $this->faker->randomFloat(2, 10, 1000),
            'unit' => $unit,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
