<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and components'],
            ['name' => 'Furniture', 'description' => 'Home and office furniture'],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office accessories'],
            ['name' => 'Hardware', 'description' => 'Tools and construction materials'],
            ['name' => 'Software', 'description' => 'Computer software and licenses'],
        ];

        foreach ($categories as $category) {
            ProductCategory::create($category);
        }
    }
}
