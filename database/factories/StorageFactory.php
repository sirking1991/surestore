<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Storage>
 */
class StorageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $storageNumber = 1;
        $capacityUnits = ['sqm', 'sqft', 'cbm', 'cbft'];
        
        return [
            'code' => 'STOR' . str_pad($storageNumber++, 3, '0', STR_PAD_LEFT),
            'name' => fake()->randomElement(['Main', 'North', 'South', 'East', 'West', 'Central']) . ' ' . fake()->randomElement(['Warehouse', 'Storage', 'Depot', 'Facility']),
            'description' => fake()->optional(0.7)->paragraph(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'manager' => fake()->name(),
            'capacity' => fake()->randomFloat(2, 100, 10000),
            'capacity_unit' => fake()->randomElement($capacityUnits),
            'is_active' => fake()->boolean(90),
            'is_main' => fake()->boolean(20),
        ];
    }
}
