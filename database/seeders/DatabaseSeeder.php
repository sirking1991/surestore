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
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
            InventoryPermissionsSeeder::class,
        ]);
        
        // Create additional users
        \App\Models\User::factory(10)->create();
        
        // Then seed master data
        $this->call([
            MasterDataSeeder::class,
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
        
        // Run purchase-related seeders in logical order
        $this->call([
            PurchaseOrderSeeder::class,    // First purchase orders
            PurchaseDeliverySeeder::class, // Then purchase deliveries
            PurchaseInvoiceSeeder::class,  // Then purchase invoices
            DisbursementSeeder::class,     // Finally disbursements
        ]);
        
        // Run production-related seeders
        $this->call([
            ProductionSeeder::class,       // Production records with materials and products
            WorkOrderSeeder::class,        // Work orders linked to productions
            WorkOrderItemSeeder::class,    // Work order items linked to work orders
        ]);
    }
}
