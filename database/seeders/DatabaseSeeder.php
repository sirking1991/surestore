<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::factory(50)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        // Stores
        Store::factory(20)->create();

        // for each store
        foreach (Store::all() as $store) {

            // create product category
            ProductCategory::factory(rand(5, 10))->create(['store_id' => $store->id]);
            
            // for each product category, create product
            // for each product, create product options

            // create order
                // create 
        }
    }
}
