<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $orderDate = fake()->dateTimeBetween('-3 months', 'now');
        $expectedDeliveryDate = fake()->dateTimeBetween($orderDate, '+1 month');
        
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $taxAmount = $subtotal * $taxRate;
        $discountAmount = $subtotal * $discountRate;
        $total = $subtotal + $taxAmount - $discountAmount;
        
        $statuses = ['draft', 'confirmed', 'received', 'cancelled'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];
        $deliveryStatuses = ['pending', 'partial', 'complete'];
        
        return [
            'code' => 'PO-' . fake()->unique()->numerify('######'),
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'order_date' => $orderDate,
            'expected_delivery_date' => $expectedDeliveryDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'status' => fake()->randomElement($statuses),
            'payment_status' => fake()->randomElement($paymentStatuses),
            'delivery_status' => fake()->randomElement($deliveryStatuses),
            'notes' => fake()->optional(0.7)->paragraph(),
        ];
    }
}
