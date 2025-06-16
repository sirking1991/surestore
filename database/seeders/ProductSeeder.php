<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some default products
        Product::factory()->create([
            'code' => 'PROD00001',
            'name' => 'Standard Product 1',
            'category' => 'General',
            'purchase_price' => 100,
            'selling_price' => 150,
            'is_active' => true,
        ]);
        
        Product::factory()->create([
            'code' => 'PROD00002',
            'name' => 'Standard Service',
            'category' => 'Services',
            'purchase_price' => 50,
            'selling_price' => 100,
            'is_active' => true,
            'is_service' => true,
        ]);
        
        // Create random products
        Product::factory()->count(30)->create();
        
        // Create a few service products
        Product::factory()->count(5)->create(['is_service' => true]);
    }
}
