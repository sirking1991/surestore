<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $invoiceNumber = 1;
        
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxRate = fake()->randomFloat(2, 0, 0.2); // 0-20% tax rate
        $discountRate = fake()->randomFloat(2, 0, 0.15); // 0-15% discount rate
        
        $tax = $subtotal * $taxRate;
        $discount = $subtotal * $discountRate;
        $total = $subtotal + $tax - $discount;
        
        $amountPaid = fake()->randomFloat(2, 0, $total);
        $amountDue = $total - $amountPaid;
        
        $invoiceDate = fake()->dateTimeBetween('-2 months', 'now');
        
        return [
            'invoice_number' => 'INV' . date('Y') . str_pad($invoiceNumber++, 5, '0', STR_PAD_LEFT),
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'order_id' => fake()->optional(0.8)->randomElement(Order::pluck('id')->toArray()),
            'invoice_date' => $invoiceDate,
            'due_date' => fake()->dateTimeBetween($invoiceDate, '+30 days'),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'amount_due' => $amountDue,
            'notes' => fake()->optional(0.7)->paragraph(),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
        ];
    }
}
