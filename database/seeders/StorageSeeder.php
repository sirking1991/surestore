<?php

namespace Database\Seeders;

use App\Models\Storage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a main storage facility
        Storage::factory()->create([
            'code' => 'STOR001',
            'name' => 'Main Warehouse',
            'is_active' => true,
            'is_main' => true,
        ]);
        
        // Create additional storage facilities
        Storage::factory()->count(3)->create();
    }
}
