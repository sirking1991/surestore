<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Illuminate\Database\Seeder;

class WorkOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all work orders to create items for
        $workOrders = WorkOrder::all();
        
        if ($workOrders->isEmpty()) {
            $this->command->info('No work orders found. Skipping work order items seeding.');
            return;
        }
        
        // Get users for assignment
        $users = User::all();
        if ($users->isEmpty()) {
            $users = [User::factory()->create()];
        }
        
        // Get products that can be produced
        $products = Product::where('can_be_produced', true)->get();
        if ($products->isEmpty()) {
            $products = Product::all();
            if ($products->isEmpty()) {
                $products = [Product::factory()->create(['can_be_produced' => true])];
            }
        }
        
        $this->command->info('Creating work order items...');
        
        // Create 2-6 items for each work order
        foreach ($workOrders as $workOrder) {
            $itemCount = rand(2, 6);
            
            // Determine item statuses based on work order status
            $itemStatuses = $this->getItemStatusesForWorkOrder($workOrder->status, $itemCount);
            
            for ($i = 0; $i < $itemCount; $i++) {
                $status = $itemStatuses[$i];
                $user = $users->random();
                $product = $products->random();
                
                // Create the work order item
                $item = WorkOrderItem::factory()
                    ->state([
                        'work_order_id' => $workOrder->id,
                        'sequence_number' => $i + 1,
                        'task_description' => $this->getTaskDescription($i, $product),
                    ])
                    ->$status()
                    ->create();
                
                // 70% chance to assign to a user
                if (rand(1, 10) <= 7) {
                    $item->update(['assigned_to' => $user->id]);
                }
                
                // 50% chance to associate with a product
                if (rand(1, 10) <= 5) {
                    $item->update([
                        'product_id' => $product->id,
                        'quantity' => rand(1, 10) + (rand(0, 100) / 100),
                    ]);
                }
            }
        }
        
        $this->command->info(WorkOrderItem::count() . ' work order items created successfully!');
    }
    
    /**
     * Get appropriate item statuses based on the work order status.
     *
     * @param string $workOrderStatus
     * @param int $count
     * @return array
     */
    private function getItemStatusesForWorkOrder(string $workOrderStatus, int $count): array
    {
        $statuses = [];
        
        switch ($workOrderStatus) {
            case 'draft':
                // All items are pending in a draft work order
                $statuses = array_fill(0, $count, 'pending');
                break;
                
            case 'scheduled':
                // Mostly pending, maybe one in progress
                $statuses = array_fill(0, $count, 'pending');
                if ($count > 1 && rand(1, 10) > 7) {
                    $statuses[0] = 'inProgress';
                }
                break;
                
            case 'in_progress':
                // Mix of completed, in progress, and pending
                $completedCount = min($count - 1, rand(0, (int)($count / 2)));
                $inProgressCount = min($count - $completedCount - 1, rand(1, 2));
                $pendingCount = $count - $completedCount - $inProgressCount;
                
                $statuses = array_merge(
                    array_fill(0, $completedCount, 'completed'),
                    array_fill(0, $inProgressCount, 'inProgress'),
                    array_fill(0, $pendingCount, 'pending')
                );
                break;
                
            case 'completed':
                // All items are completed
                $statuses = array_fill(0, $count, 'completed');
                break;
                
            case 'cancelled':
                // Mix of cancelled and maybe some completed
                $completedCount = rand(0, (int)($count / 3));
                $cancelledCount = $count - $completedCount;
                
                $statuses = array_merge(
                    array_fill(0, $completedCount, 'completed'),
                    array_fill(0, $cancelledCount, 'cancelled')
                );
                break;
                
            default:
                $statuses = array_fill(0, $count, 'pending');
        }
        
        // Shuffle to randomize the order
        shuffle($statuses);
        
        return $statuses;
    }
    
    /**
     * Generate a task description based on sequence and product.
     *
     * @param int $sequence
     * @param Product $product
     * @return string
     */
    private function getTaskDescription(int $sequence, Product $product): string
    {
        $tasks = [
            'Prepare materials for %s',
            'Assemble components for %s',
            'Quality check %s',
            'Package %s for delivery',
            'Process raw materials for %s',
            'Cut materials for %s production',
            'Apply finishing to %s',
            'Test functionality of %s',
            'Prepare workstation for %s assembly',
            'Clean equipment after %s production',
        ];
        
        $task = $tasks[$sequence % count($tasks)];
        return sprintf($task, $product->name);
    }
}
