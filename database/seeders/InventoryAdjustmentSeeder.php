<?php

namespace Database\Seeders;

use App\Models\InventoryAdjustment;
use App\Models\InventoryAdjustmentItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 inventory adjustments with 2-5 items each
        InventoryAdjustment::factory()
            ->count(10)
            ->create()
            ->each(function ($adjustment) {
                // Create 2-5 items for each adjustment
                $itemCount = rand(2, 5);
                
                // Create items directly related to this adjustment
                InventoryAdjustmentItem::factory()
                    ->count($itemCount)
                    ->create([
                        'inventory_adjustment_id' => $adjustment->id,
                    ]);
            });
    }
}
