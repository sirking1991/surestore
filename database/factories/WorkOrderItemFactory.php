<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrderItem>
 */
class WorkOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'in_progress', 'completed', 'cancelled']);
        $estimatedMinutes = $this->faker->numberBetween(15, 240);
        
        $startedAt = null;
        $completedAt = null;
        $actualMinutes = null;
        
        if ($status === 'in_progress' || $status === 'completed') {
            $startedAt = $this->faker->dateTimeBetween('-1 week', 'now');
            
            if ($status === 'completed') {
                $completedAt = Carbon::instance($startedAt)->addMinutes(
                    $this->faker->numberBetween($estimatedMinutes * 0.7, $estimatedMinutes * 1.3)
                );
                $actualMinutes = $startedAt->diff($completedAt)->i + ($startedAt->diff($completedAt)->h * 60);
            }
        }
        
        return [
            'work_order_id' => WorkOrder::factory(),
            'task_description' => $this->faker->sentence(),
            'sequence_number' => $this->faker->numberBetween(1, 10),
            'status' => $status,
            'estimated_minutes' => $estimatedMinutes,
            'actual_minutes' => $actualMinutes,
            'assigned_to' => $this->faker->boolean(70) ? User::factory() : null,
            'notes' => $this->faker->boolean(60) ? $this->faker->paragraph() : null,
            'product_id' => $this->faker->boolean(50) ? Product::factory() : null,
            'quantity' => $this->faker->boolean(50) ? $this->faker->randomFloat(2, 1, 100) : null,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ];
    }
    
    /**
     * Indicate that the work order item is pending.
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'started_at' => null,
                'completed_at' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate that the work order item is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $startedAt = $this->faker->dateTimeBetween('-3 days', 'now');
            
            return [
                'status' => 'in_progress',
                'started_at' => $startedAt,
                'completed_at' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate that the work order item is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startedAt = $this->faker->dateTimeBetween('-1 week', '-1 day');
            $estimatedMinutes = $attributes['estimated_minutes'] ?? $this->faker->numberBetween(15, 240);
            $completedAt = Carbon::instance($startedAt)->addMinutes(
                $this->faker->numberBetween($estimatedMinutes * 0.7, $estimatedMinutes * 1.3)
            );
            $actualMinutes = $startedAt->diff($completedAt)->i + ($startedAt->diff($completedAt)->h * 60);
            
            return [
                'status' => 'completed',
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'actual_minutes' => $actualMinutes,
            ];
        });
    }
    
    /**
     * Indicate that the work order item is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'started_at' => null,
                'completed_at' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Configure the model factory to associate with a specific work order.
     */
    public function forWorkOrder(WorkOrder $workOrder): static
    {
        return $this->state(function (array $attributes) use ($workOrder) {
            return [
                'work_order_id' => $workOrder->id,
            ];
        });
    }
    
    /**
     * Configure the model factory to associate with a specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'product_id' => $product->id,
            ];
        });
    }
    
    /**
     * Configure the model factory to assign to a specific user.
     */
    public function assignedTo(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'assigned_to' => $user->id,
            ];
        });
    }
}
