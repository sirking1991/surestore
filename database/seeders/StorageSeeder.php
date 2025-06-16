<?php

namespace Database\Seeders;

use App\Models\Storage;
use App\Models\StorageLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating storage facilities...');
        
        // Create main storage
        $mainStorage = Storage::create([
            'code' => 'STOR001',
            'name' => 'Main Warehouse',
            'description' => 'Main warehouse for storing products',
            'address' => '123 Main Street',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'phone' => '123-456-7890',
            'manager' => 'John Doe',
            'capacity' => 1000,
            'capacity_unit' => 'sqm',
            'is_active' => true,
            'is_main' => true,
        ]);
        
        // Create additional storages with unique codes
        $otherStorages = Storage::factory()
            ->count(3)
            ->sequence(fn ($sequence) => ['code' => 'STOR' . sprintf('%03d', $sequence->index + 2)])
            ->create();
        
        // Combine all storages for location creation
        $allStorages = collect([$mainStorage])->merge($otherStorages);
        
        $this->command->info('Creating storage locations...');
        
        // Create locations for each storage
        foreach ($allStorages as $storage) {
            // Create 5-10 locations per storage
            $locationCount = rand(5, 10);
            
            $this->command->info("Creating {$locationCount} locations for {$storage->name}");
            
            StorageLocation::factory()
                ->count($locationCount)
                ->create([
                    'storage_id' => $storage->id,
                ]);
        }
        
        $this->command->info('Storage facilities and locations created successfully!');
    }
}
