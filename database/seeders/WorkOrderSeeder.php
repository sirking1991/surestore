<?php

namespace Database\Seeders;

use App\Models\Production;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all productions that are not cancelled
        $productions = Production::where('status', '!=', 'cancelled')->get();
        
        if ($productions->isEmpty()) {
            // Create some productions if none exist
            Production::factory()->count(5)->create();
            $productions = Production::all();
        }
        
        // Get all users
        $users = User::all();
        
        if ($users->isEmpty()) {
            // Create some users if none exist
            User::factory()->count(3)->create();
            $users = User::all();
        }
        
        // Create work orders with different statuses
        foreach ($productions as $production) {
            // Create 1-3 work orders per production
            $workOrderCount = rand(1, 3);
            
            // Create draft work orders
            WorkOrder::factory()
                ->count(rand(0, $workOrderCount))
                ->draft()
                ->forProduction($production)
                ->create();
                
            // Create scheduled work orders
            WorkOrder::factory()
                ->count(rand(0, $workOrderCount))
                ->scheduled()
                ->forProduction($production)
                ->create();
                
            // Create in-progress work orders
            WorkOrder::factory()
                ->count(rand(0, $workOrderCount))
                ->inProgress()
                ->forProduction($production)
                ->create();
                
            // Create completed work orders
            WorkOrder::factory()
                ->count(rand(0, $workOrderCount))
                ->completed()
                ->forProduction($production)
                ->create();
                
            // Create cancelled work orders (fewer of these)
            if (rand(0, 10) > 7) {
                WorkOrder::factory()
                    ->count(rand(1, 2))
                    ->cancelled()
                    ->forProduction($production)
                    ->create();
            }
        }
        
        // Assign users to work orders
        WorkOrder::all()->each(function ($workOrder) use ($users) {
            // Randomly assign a creator and assignee
            $workOrder->update([
                'user_id' => $users->random()->id,
                'assigned_to' => rand(0, 10) > 2 ? $users->random()->id : null,
            ]);
        });
    }
}
