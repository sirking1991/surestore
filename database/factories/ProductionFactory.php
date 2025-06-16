<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Production>
 */
class ProductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['planned', 'in_progress', 'completed', 'cancelled']);
        $productionDate = $this->faker->dateTimeBetween('-6 months', 'now');
        
        // Only completed productions have start and end times
        $startTime = null;
        $endTime = null;
        
        if ($status === 'completed' || $status === 'in_progress') {
            $startTime = clone $productionDate;
            $startTime->modify('+1 hour'); // Start time is after production date
        }
        
        if ($status === 'completed' && $startTime) {
            $endTime = clone $startTime;
            $endTime->modify('+' . $this->faker->numberBetween(2, 24) . ' hours');
        }
        
        // Only completed productions have labor details
        $laborMinutes = $status === 'completed' ? $this->faker->numberBetween(30, 480) : null;
        $setupMinutes = $status === 'completed' ? $this->faker->numberBetween(15, 120) : null;
        $laborCost = $status === 'completed' ? $this->faker->randomFloat(2, 50, 500) : null;
        
        return [
            'batch_number' => 'PROD-' . $this->faker->unique()->numerify('######'),
            'description' => $this->faker->optional(0.8)->sentence(),
            'production_date' => $productionDate,
            'status' => $status,
            'total_cost' => $this->faker->randomFloat(2, 100, 5000),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'labor_minutes' => $laborMinutes,
            'setup_minutes' => $setupMinutes,
            'labor_cost' => $laborCost,
            'user_id' => User::inRandomOrder()->first()?->id,
        ];
    }
    
    /**
     * Indicate that the production is planned.
     */
    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
            'start_time' => null,
            'end_time' => null,
            'labor_minutes' => null,
            'setup_minutes' => null,
            'labor_cost' => null,
        ]);
    }
    
    /**
     * Indicate that the production is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $productionDate = $this->faker->dateTimeBetween('-2 days', 'now');
            $startTime = clone $productionDate;
            $startTime->modify('+1 hour');
            
            return [
                'status' => 'in_progress',
                'production_date' => $productionDate,
                'start_time' => $startTime,
                'end_time' => null,
                'labor_minutes' => null,
                'setup_minutes' => null,
                'labor_cost' => null,
            ];
        });
    }
    
    /**
     * Indicate that the production is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $productionDate = $this->faker->dateTimeBetween('-7 days', '-1 day');
            
            $startTime = clone $productionDate;
            $startTime->modify('+1 hour');
            
            $endTime = clone $startTime;
            $endTime->modify('+' . $this->faker->numberBetween(2, 24) . ' hours');
            
            $laborMinutes = $this->faker->numberBetween(30, 480);
            $setupMinutes = $this->faker->numberBetween(15, 120);
            
            return [
                'status' => 'completed',
                'production_date' => $productionDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'labor_minutes' => $laborMinutes,
                'setup_minutes' => $setupMinutes,
                'labor_cost' => $this->faker->randomFloat(2, 50, 500),
            ];
        });
    }
}
