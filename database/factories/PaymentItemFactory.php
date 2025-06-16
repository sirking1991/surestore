<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentItem>
 */
class PaymentItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $invoice = Invoice::inRandomOrder()->first();
        
        return [
            'payment_id' => Payment::inRandomOrder()->first()->id,
            'invoice_id' => $invoice->id,
            'invoice_item_id' => fake()->optional(0.5)->randomElement(
                InvoiceItem::where('invoice_id', $invoice->id)->pluck('id')->toArray()
            ),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
