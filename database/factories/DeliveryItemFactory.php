<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Storage;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryItem>
 */
class DeliveryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $quantity = fake()->randomFloat(2, 1, 20);
        $quantityReceived = fake()->randomFloat(2, 0, $quantity);
        
        $packageTypes = ['Box', 'Pallet', 'Envelope', 'Crate', 'Bag', 'Tube', 'Container'];
        $weightUnits = ['kg', 'g', 'lb', 'oz'];
        $volumeUnits = ['m3', 'cm3', 'ft3', 'in3'];
        
        // Get a random storage and one of its locations if any exist
        $storage = Storage::inRandomOrder()->first();
        $storageLocation = null;
        
        // Only assign a storage location 70% of the time when storage is assigned
        if ($storage && fake()->boolean(70)) {
            $storageLocation = StorageLocation::where('storage_id', $storage->id)
                ->inRandomOrder()
                ->first();
        }
        
        return [
            'delivery_id' => Delivery::inRandomOrder()->first()->id,
            'product_id' => $product->id,
            'order_item_id' => fake()->optional(0.7)->randomElement(OrderItem::pluck('id')->toArray()),
            'storage_id' => $storage ? fake()->optional(0.8)->randomElement([$storage->id]) : null,
            'storage_location_id' => $storageLocation ? $storageLocation->id : null,
            'description' => fake()->optional(0.3)->sentence(),
            'quantity' => $quantity,
            'quantity_received' => $quantityReceived,
            'unit' => $product->unit,
            'weight' => fake()->optional(0.6)->randomFloat(2, 0.1, 100),
            'weight_unit' => fake()->randomElement($weightUnits),
            'volume' => fake()->optional(0.4)->randomFloat(2, 0.01, 10),
            'volume_unit' => fake()->randomElement($volumeUnits),
            'package_type' => fake()->optional(0.7)->randomElement($packageTypes),
            'serial_number' => fake()->optional(0.3)->bothify('SN-####-????-####'),
            'lot_number' => fake()->optional(0.3)->bothify('LOT-####-??'),
            'sort_order' => fake()->numberBetween(1, 10),
            'notes' => fake()->optional(0.2)->sentence(),
            'status' => fake()->randomElement(['pending', 'shipped', 'delivered', 'returned', 'damaged']),
        ];
    }
}
