<?php

namespace Database\Factories;

use App\Models\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageLocation>
 */
class StorageLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $locationNumber = 1;
        $capacityUnits = ['sqm', 'sqft', 'cbm', 'cbft'];
        
        $zones = ['A', 'B', 'C', 'D', 'E'];
        $aisles = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
        $racks = ['R1', 'R2', 'R3', 'R4', 'R5'];
        $shelves = ['S1', 'S2', 'S3', 'S4'];
        $bins = ['B01', 'B02', 'B03', 'B04', 'B05'];
        
        $zone = fake()->randomElement($zones);
        $aisle = fake()->randomElement($aisles);
        $rack = fake()->randomElement($racks);
        $shelf = fake()->randomElement($shelves);
        $bin = fake()->randomElement($bins);
        
        return [
            'storage_id' => Storage::factory(),
            'code' => 'LOC' . str_pad($locationNumber++, 3, '0', STR_PAD_LEFT),
            'name' => "Location {$zone}-{$aisle}-{$rack}-{$shelf}-{$bin}",
            'zone' => $zone,
            'aisle' => $aisle,
            'rack' => $rack,
            'shelf' => $shelf,
            'bin' => $bin,
            'description' => fake()->optional(0.7)->sentence(),
            'capacity' => fake()->randomFloat(2, 1, 100),
            'capacity_unit' => fake()->randomElement($capacityUnits),
            'is_active' => fake()->boolean(90),
        ];
    }
}
