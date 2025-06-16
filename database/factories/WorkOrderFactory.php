<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduledDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $status = $this->faker->randomElement(['draft', 'scheduled', 'in_progress', 'completed', 'cancelled']);
        
        $startTime = null;
        $endTime = null;
        $actualMinutes = null;
        
        if (in_array($status, ['in_progress', 'completed'])) {
            $startTime = Carbon::instance($scheduledDate)->addHours($this->faker->numberBetween(0, 8));
            
            if ($status === 'completed') {
                $actualMinutes = $this->faker->numberBetween(30, 480); // 30 min to 8 hours
                $endTime = (clone $startTime)->addMinutes($actualMinutes);
            }
        }
        
        $estimatedMinutes = $this->faker->numberBetween(30, 480); // 30 min to 8 hours
        
        return [
            'order_number' => 'WO-' . $this->faker->unique()->numerify('######'),
            'production_id' => Production::factory(),
            'description' => $this->faker->sentence(10),
            'scheduled_date' => $scheduledDate,
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => $status,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'estimated_minutes' => $estimatedMinutes,
            'actual_minutes' => $actualMinutes,
            'notes' => $this->faker->boolean(70) ? $this->faker->paragraphs(2, true) : null,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'assigned_to' => $this->faker->boolean(80) ? User::inRandomOrder()->first()?->id : null,
        ];
    }
    
    /**
     * Indicate the work order is in draft status.
     */
    public function draft(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
                'start_time' => null,
                'end_time' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate the work order is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'scheduled',
                'start_time' => null,
                'end_time' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate the work order is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = Carbon::instance($attributes['scheduled_date'])->addHours($this->faker->numberBetween(0, 8));
            
            return [
                'status' => 'in_progress',
                'start_time' => $startTime,
                'end_time' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate the work order is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = Carbon::instance($attributes['scheduled_date'])->addHours($this->faker->numberBetween(0, 8));
            $actualMinutes = $this->faker->numberBetween(30, 480); // 30 min to 8 hours
            $endTime = (clone $startTime)->addMinutes($actualMinutes);
            
            return [
                'status' => 'completed',
                'start_time' => $startTime,
                'end_time' => $endTime,
                'actual_minutes' => $actualMinutes,
            ];
        });
    }
    
    /**
     * Indicate the work order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'start_time' => null,
                'end_time' => null,
                'actual_minutes' => null,
            ];
        });
    }
    
    /**
     * Indicate the work order belongs to a specific production.
     */
    public function forProduction(Production $production): static
    {
        return $this->state(function (array $attributes) use ($production) {
            return [
                'production_id' => $production->id,
            ];
        });
    }
    
    /**
     * Indicate the work order is created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user->id,
            ];
        });
    }
    
    /**
     * Indicate the work order is assigned to a specific user.
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
