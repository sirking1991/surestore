<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users first
        \App\Models\User::factory(10)->create();
        
        // Then seed customers and products
        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
        ]);
        
        // Seed transactions in logical order
        $this->call([
            QuoteSeeder::class,     // First quotes
            OrderSeeder::class,     // Then orders (can be linked to quotes)
            DeliverySeeder::class,  // Then deliveries (linked to orders)
            InvoiceSeeder::class,   // Then invoices (linked to orders)
            PaymentSeeder::class,   // Finally payments (linked to invoices)
        ]);
        
        // Run our other seeders
        $this->call([
            SupplierSeeder::class,
            StorageSeeder::class,
        ]);
    }
}
