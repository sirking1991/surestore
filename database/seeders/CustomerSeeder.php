<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default customer
        Customer::factory()->create([
            'code' => 'CUST00001',
            'name' => 'Default Customer',
            'is_active' => true,
        ]);
        
        // Create random customers
        Customer::factory()->count(20)->create();
    }
}
