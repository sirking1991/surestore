<?php

namespace Database\Factories;

use App\Models\StorageLocation;
use App\Models\User;
use App\Models\InventoryAdjustment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryAdjustment>
 */
class InventoryAdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['draft', 'approved', 'cancelled'];
        $adjustmentTypes = ['addition', 'subtraction', 'transfer'];
        
        // Get random storage location and user IDs
        $storageLocationId = StorageLocation::inRandomOrder()->first()?->id ?? 1;
        $userId = User::inRandomOrder()->first()?->id ?? 1;
        
        $status = $this->faker->randomElement($statuses);
        $approvedBy = null;
        $approvedAt = null;
        
        if ($status === 'approved') {
            $approvedBy = User::inRandomOrder()->first()?->id ?? 1;
            $approvedAt = now()->subDays(rand(1, 30));
        }
        
        return [
            'reference_number' => 'ADJ-' . $this->faker->unique()->numerify('######'),
            'adjustment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'storage_location_id' => $storageLocationId,
            'adjustment_type' => $this->faker->randomElement($adjustmentTypes),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'status' => $status,
            'created_by' => $userId,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
        ];
    }
}
