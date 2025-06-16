<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseDelivery>
 */
class PurchaseDeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchaseOrder = PurchaseOrder::inRandomOrder()->first();
        $supplier = $purchaseOrder ? $purchaseOrder->supplier : Supplier::inRandomOrder()->first();
        
        $deliveryDate = fake()->dateTimeBetween('-2 months', 'now');
        $expectedDeliveryDate = fake()->optional(0.7)->dateTimeBetween($deliveryDate, '+2 weeks');
        
        $carriers = ['FedEx', 'UPS', 'DHL', 'USPS', 'Local Delivery'];
        $shippingMethods = ['Ground', 'Express', 'Overnight', 'Standard', 'Economy'];
        $statuses = ['pending', 'in-transit', 'delivered', 'cancelled'];
        
        $shippingCost = fake()->randomFloat(2, 0, 200);
        $otherCharges = fake()->randomFloat(2, 0, 100);
        
        $weightUnits = ['kg', 'g', 'lb', 'oz'];
        $volumeUnits = ['m3', 'cm3', 'ft3', 'in3'];
        
        return [
            'code' => 'PD-' . fake()->unique()->numerify('######'),
            'supplier_id' => $supplier->id,
            'purchase_order_id' => fake()->optional(0.8)->randomElement([$purchaseOrder->id ?? null]),
            'delivery_date' => $deliveryDate,
            'expected_delivery_date' => $expectedDeliveryDate,
            'tracking_number' => fake()->optional(0.7)->bothify('?###?###?###'),
            'carrier' => fake()->optional(0.8)->randomElement($carriers),
            'shipping_method' => fake()->optional(0.7)->randomElement($shippingMethods),
            'shipping_cost' => $shippingCost,
            'other_charges' => $otherCharges,
            'total_weight' => fake()->optional(0.6)->randomFloat(2, 1, 1000),
            'weight_unit' => fake()->randomElement($weightUnits),
            'total_volume' => fake()->optional(0.4)->randomFloat(2, 0.1, 10),
            'volume_unit' => fake()->randomElement($volumeUnits),
            'status' => fake()->randomElement($statuses),
            'notes' => fake()->optional(0.6)->paragraph(),
        ];
    }
}
