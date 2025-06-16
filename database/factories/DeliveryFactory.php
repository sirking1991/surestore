<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $deliveryNumber = 1;
        
        $carriers = ['FedEx', 'UPS', 'DHL', 'USPS', 'Local Courier', 'Self Pickup'];
        $deliveryDate = fake()->dateTimeBetween('-1 month', '+2 weeks');
        
        return [
            'delivery_number' => 'DEL' . date('Y') . str_pad($deliveryNumber++, 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'order_id' => fake()->optional(0.8)->randomElement(Order::pluck('id')->toArray()),
            'delivery_date' => $deliveryDate,
            'scheduled_date' => fake()->optional(0.8)->dateTimeBetween('-2 weeks', '+2 weeks'),
            'shipping_address' => fake()->address(),
            'tracking_number' => fake()->optional(0.7)->bothify('??##?#####?##?'),
            'carrier' => fake()->optional(0.8)->randomElement($carriers),
            'shipping_cost' => fake()->randomFloat(2, 0, 200),
            'notes' => fake()->optional(0.6)->paragraph(),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'returned', 'cancelled']),
        ];
    }
}
