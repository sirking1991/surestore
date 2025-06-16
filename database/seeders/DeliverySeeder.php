<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create deliveries
        Delivery::factory()->count(15)->create()->each(function ($delivery) {
            // For each delivery, create 1-6 delivery items
            $itemCount = rand(1, 6);
            
            // Create delivery items for this delivery
            DeliveryItem::factory()->count($itemCount)->create([
                'delivery_id' => $delivery->id
            ]);
        });
    }
}
