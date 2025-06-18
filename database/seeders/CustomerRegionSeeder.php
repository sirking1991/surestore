<?php

namespace Database\Seeders;

use App\Models\CustomerRegion;
use Illuminate\Database\Seeder;

class CustomerRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['name' => 'North', 'description' => 'Northern region'],
            ['name' => 'South', 'description' => 'Southern region'],
            ['name' => 'East', 'description' => 'Eastern region'],
            ['name' => 'West', 'description' => 'Western region'],
            ['name' => 'Central', 'description' => 'Central region'],
        ];

        foreach ($regions as $region) {
            CustomerRegion::create($region);
        }
    }
}
