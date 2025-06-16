<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionMaterial;
use App\Models\ProductionProduct;
use App\Models\Storage;
use App\Models\StorageLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some products to use as raw materials and finished products
        $products = Product::all();
        
        if ($products->isEmpty()) {
            // Create some products if none exist
            $products = Product::factory()->count(10)->create();
        }
        
        // Get available storage locations
        $storages = Storage::all();
        
        if ($storages->isEmpty()) {
            // Create a default storage if none exist
            $storages = [Storage::factory()->create(['name' => 'Main Storage'])];
        }
        
        // Get or create storage locations
        $storageLocations = StorageLocation::all();
        
        if ($storageLocations->isEmpty()) {
            // Create some storage locations if none exist
            foreach ($storages as $storage) {
                StorageLocation::factory()->count(3)->create([
                    'storage_id' => $storage->id
                ]);
            }
            $storageLocations = StorageLocation::all();
        }
        
        // Create 20 production records with different statuses
        Production::factory()->count(5)->planned()->create();
        Production::factory()->count(5)->inProgress()->create();
        Production::factory()->count(10)->completed()->create();
        
        // For each production, create materials and products
        Production::all()->each(function ($production) use ($products, $storages, $storageLocations) {
            // Skip creating materials and products for cancelled productions
            if ($production->status === 'cancelled') {
                return;
            }
            
            // Create 2-5 material entries for each production
            $materialCount = rand(2, 5);
            $materialProducts = $products->random($materialCount);
            
            $materialTotalCost = 0;
            
            foreach ($materialProducts as $product) {
                $quantity = rand(5, 50) / 2; // Random quantity with 0.5 precision
                $unitCost = rand(10, 100) / 2; // Random unit cost with 0.5 precision
                $totalCost = $quantity * $unitCost;
                $materialTotalCost += $totalCost;
                
                // Get a random storage and one of its locations
                $storage = $storages->random();
                $storageLocation = $storageLocations
                    ->where('storage_id', $storage->id)
                    ->random();
                
                ProductionMaterial::create([
                    'production_id' => $production->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'storage_id' => $storage->id,
                    'storage_location_id' => $storageLocation?->id,
                ]);
            }
            
            // Create 1-3 product entries for each production
            $productCount = rand(1, 3);
            $outputProducts = $products->random($productCount);
            
            // Add a markup for the finished products (10-50% markup)
            $markup = 1 + (rand(10, 50) / 100);
            $productTotalCost = $materialTotalCost * $markup;
            
            // Distribute the total cost among the output products
            $remainingCost = $productTotalCost;
            $lastIndex = $outputProducts->count() - 1;
            
            foreach ($outputProducts as $index => $product) {
                $quantity = rand(1, 20) / 2; // Random quantity with 0.5 precision
                
                // For all but the last product, assign a portion of the total cost
                if ($index < $lastIndex) {
                    $portion = rand(10, 70) / 100; // Use 10-70% of the remaining cost
                    $totalCost = $remainingCost * $portion;
                    $remainingCost -= $totalCost;
                } else {
                    // Last product gets the remaining cost
                    $totalCost = $remainingCost;
                }
                
                $unitCost = $totalCost / $quantity;
                
                // Get a random storage and one of its locations
                $storage = $storages->random();
                $storageLocation = $storageLocations
                    ->where('storage_id', $storage->id)
                    ->random();
                
                ProductionProduct::create([
                    'production_id' => $production->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'storage_id' => $storage->id,
                    'storage_location_id' => $storageLocation?->id,
                ]);
            }
            
            // Update the production's total cost
            $production->update([
                'total_cost' => $materialTotalCost + ($production->labor_cost ?? 0)
            ]);
        });
    }
}
