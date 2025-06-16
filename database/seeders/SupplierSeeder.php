<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default supplier
        Supplier::factory()->create([
            'code' => 'SUPP00001',
            'name' => 'Default Supplier',
            'is_active' => true,
        ]);
        
        // Create random suppliers
        Supplier::factory()->count(15)->create();
    }
}
