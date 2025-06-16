<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Production;
use App\Models\Storage;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductionMaterial>
 */
class ProductionMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $unitCost = $this->faker->randomFloat(2, 5, 500);
        $totalCost = $quantity * $unitCost;
        
        // Get a random storage
        $storage = Storage::inRandomOrder()->first();
        
        // Get a random storage location from that storage if available
        $storageLocationId = null;
        if ($storage) {
            $storageLocation = StorageLocation::where('storage_id', $storage->id)
                ->inRandomOrder()
                ->first();
            $storageLocationId = $storageLocation?->id;
        }
        
        return [
            'production_id' => Production::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $totalCost,
            'storage_id' => $storage?->id,
            'storage_location_id' => $storageLocationId,
        ];
    }
    
    /**
     * Indicate that the production material belongs to a specific production.
     */
    public function forProduction(Production $production): static
    {
        return $this->state(fn (array $attributes) => [
            'production_id' => $production->id,
        ]);
    }
    
    /**
     * Indicate that the production material is for a specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }
    
    /**
     * Indicate that the production material is stored at a specific storage location.
     */
    public function atStorageLocation(StorageLocation $storageLocation): static
    {
        return $this->state(fn (array $attributes) => [
            'storage_id' => $storageLocation->storage_id,
            'storage_location_id' => $storageLocation->id,
        ]);
    }
}
