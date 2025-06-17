<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Storage;
use App\Models\StorageLocation;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create customers
        $this->createCustomers();
        
        // Create suppliers
        $this->createSuppliers();
        
        // Create storages
        $this->createStorages();
        
        // Create products
        $this->createProducts();
    }
    
    /**
     * Create sample customers
     */
    private function createCustomers(): void
    {
        $customers = [
            [
                'code' => 'CUST001',
                'name' => 'PT Maju Bersama',
                'email' => 'contact@majubersama.com',
                'phone' => '021-5551234',
                'address' => 'Jl. Sudirman No. 123',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '10220',
                'country' => 'Indonesia',
                'credit_limit' => 50000000,
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'CUST002',
                'name' => 'CV Sejahtera Abadi',
                'email' => 'info@sejahteraabadi.co.id',
                'phone' => '022-7654321',
                'address' => 'Jl. Asia Afrika No. 88',
                'city' => 'Bandung',
                'state' => 'Jawa Barat',
                'postal_code' => '40112',
                'country' => 'Indonesia',
                'credit_limit' => 25000000,
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'CUST003',
                'name' => 'UD Makmur Jaya',
                'email' => 'sales@makmurjaya.id',
                'phone' => '031-3334444',
                'address' => 'Jl. Raya Darmo No. 45',
                'city' => 'Surabaya',
                'state' => 'Jawa Timur',
                'postal_code' => '60264',
                'country' => 'Indonesia',
                'credit_limit' => 15000000,
                'balance' => 0,
                'is_active' => true,
            ],
        ];
        
        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
    
    /**
     * Create sample suppliers
     */
    private function createSuppliers(): void
    {
        $suppliers = [
            [
                'code' => 'SUPP001',
                'name' => 'PT Sumber Makmur',
                'email' => 'purchasing@sumbermakmur.com',
                'phone' => '021-9876543',
                'address' => 'Jl. Gatot Subroto No. 55',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '10270',
                'country' => 'Indonesia',
                'tax_id' => '01.234.567.8-901.000',
                'bank_name' => 'Bank Central Asia',
                'bank_account' => '1234567890',
                'bank_account_name' => 'PT Sumber Makmur',
                'credit_limit' => 100000000,
                'balance' => 0,
                'is_active' => true,
            ],
            [
                'code' => 'SUPP002',
                'name' => 'CV Mitra Utama',
                'email' => 'sales@mitrautama.co.id',
                'phone' => '022-1112222',
                'address' => 'Jl. Merdeka No. 78',
                'city' => 'Bandung',
                'state' => 'Jawa Barat',
                'postal_code' => '40115',
                'country' => 'Indonesia',
                'tax_id' => '02.345.678.9-012.000',
                'bank_name' => 'Bank Mandiri',
                'bank_account' => '9876543210',
                'bank_account_name' => 'CV Mitra Utama',
                'credit_limit' => 75000000,
                'balance' => 0,
                'is_active' => true,
            ],
        ];
        
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
    
    /**
     * Create sample storages and storage locations
     */
    private function createStorages(): void
    {
        // Create main warehouse
        $mainWarehouse = Storage::create([
            'code' => 'WH001',
            'name' => 'Main Warehouse',
            'description' => 'Main storage facility for finished goods',
            'address' => 'Jl. Industri No. 123',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '10610',
            'country' => 'Indonesia',
            'phone' => '021-5559876',
            'manager' => 'Budi Santoso',
            'capacity' => 1000,
            'capacity_unit' => 'sqm',
            'is_active' => true,
            'is_main' => true,
        ]);
        
        // Create secondary warehouse
        $secondaryWarehouse = Storage::create([
            'code' => 'WH002',
            'name' => 'Secondary Warehouse',
            'description' => 'Secondary storage facility for raw materials',
            'address' => 'Jl. Industri No. 456',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'postal_code' => '10610',
            'country' => 'Indonesia',
            'phone' => '021-5559877',
            'manager' => 'Dewi Lestari',
            'capacity' => 750,
            'capacity_unit' => 'sqm',
            'is_active' => true,
            'is_main' => false,
        ]);
        
        // Create storage locations for main warehouse
        $mainLocations = [
            [
                'code' => 'A',
                'name' => 'Section A',
                'zone' => 'A',
                'capacity' => 200,
                'capacity_unit' => 'sqm',
                'is_active' => true,
            ],
            [
                'code' => 'B',
                'name' => 'Section B',
                'zone' => 'B',
                'capacity' => 200,
                'capacity_unit' => 'sqm',
                'is_active' => true,
            ],
        ];
        
        foreach ($mainLocations as $location) {
            $mainWarehouse->locations()->create($location);
        }
        
        // Create storage locations for secondary warehouse
        $secondaryLocations = [
            [
                'code' => 'RM',
                'name' => 'Raw Materials',
                'zone' => 'RM',
                'capacity' => 300,
                'capacity_unit' => 'sqm',
                'is_active' => true,
            ],
            [
                'code' => 'WIP',
                'name' => 'Work in Progress',
                'zone' => 'WIP',
                'capacity' => 200,
                'capacity_unit' => 'sqm',
                'is_active' => true,
            ],
        ];
        
        foreach ($secondaryLocations as $location) {
            $secondaryWarehouse->locations()->create($location);
        }
    }
    
    /**
     * Create sample products
     */
    private function createProducts(): void
    {
        $suppliers = Supplier::all();
        $locations = StorageLocation::all();
        
        $products = [
            [
                'code' => 'P001',
                'name' => 'Office Chair',
                'description' => 'Ergonomic office chair with adjustable height',
                'unit' => 'pcs',
                'purchase_price' => 750000,
                'selling_price' => 1200000,
                'stock' => 25,
                'min_stock' => 5,
                'category' => 'Furniture',
                'brand' => 'OfficePro',
                'is_active' => true,
                'is_service' => false,
                'supplier_id' => $suppliers->random()->id,
                'storage_location_id' => $locations->random()->id,
            ],
            [
                'code' => 'P002',
                'name' => 'Office Desk',
                'description' => 'Standard office desk 120x60cm',
                'unit' => 'pcs',
                'purchase_price' => 1200000,
                'selling_price' => 1800000,
                'stock' => 15,
                'min_stock' => 3,
                'category' => 'Furniture',
                'brand' => 'OfficePro',
                'is_active' => true,
                'is_service' => false,
                'supplier_id' => $suppliers->random()->id,
                'storage_location_id' => $locations->random()->id,
            ],
            [
                'code' => 'P003',
                'name' => 'Filing Cabinet',
                'description' => '4-drawer metal filing cabinet',
                'unit' => 'pcs',
                'purchase_price' => 850000,
                'selling_price' => 1350000,
                'stock' => 10,
                'min_stock' => 2,
                'category' => 'Furniture',
                'brand' => 'StorageMaster',
                'is_active' => true,
                'is_service' => false,
                'supplier_id' => $suppliers->random()->id,
                'storage_location_id' => $locations->random()->id,
            ],
            [
                'code' => 'P004',
                'name' => 'Laptop Computer',
                'description' => '15" laptop with i5 processor, 8GB RAM, 256GB SSD',
                'unit' => 'pcs',
                'purchase_price' => 8500000,
                'selling_price' => 10500000,
                'stock' => 8,
                'min_stock' => 2,
                'category' => 'Electronics',
                'brand' => 'TechPro',
                'is_active' => true,
                'is_service' => false,
                'supplier_id' => $suppliers->random()->id,
                'storage_location_id' => $locations->random()->id,
            ],
            [
                'code' => 'S001',
                'name' => 'Office Cleaning',
                'description' => 'Regular office cleaning service',
                'unit' => 'service',
                'purchase_price' => 0,
                'selling_price' => 1500000,
                'stock' => 0,
                'min_stock' => 0,
                'category' => 'Services',
                'brand' => 'CleanCo',
                'is_active' => true,
                'is_service' => true,
                'supplier_id' => null,
                'storage_location_id' => null,
            ],
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
