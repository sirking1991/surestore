<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InventoryPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions for Customer entity
        $customerPermissions = [
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'restore customers',
            'force delete customers',
        ];
        
        // Create permissions for Supplier entity
        $supplierPermissions = [
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'restore suppliers',
            'force delete suppliers',
        ];
        
        // Create permissions for Storage entity
        $storagePermissions = [
            'view storages',
            'create storages',
            'edit storages',
            'delete storages',
            'restore storages',
            'force delete storages',
        ];
        
        // Create permissions for StorageLocation entity
        $locationPermissions = [
            'view storage locations',
            'create storage locations',
            'edit storage locations',
            'delete storage locations',
            'restore storage locations',
            'force delete storage locations',
        ];
        
        // Create permissions for Product entity
        $productPermissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'restore products',
            'force delete products',
        ];
        
        // Combine all permissions
        $allPermissions = array_merge(
            $customerPermissions,
            $supplierPermissions,
            $storagePermissions,
            $locationPermissions,
            $productPermissions
        );
        
        // Create permissions in database
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assign permissions to existing roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($allPermissions);
        }
        
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            // Managers can view, create, and edit but not delete
            $managerPermissions = array_filter($allPermissions, function($permission) {
                return !str_contains($permission, 'delete') && 
                       !str_contains($permission, 'restore') && 
                       !str_contains($permission, 'force delete');
            });
            $managerRole->givePermissionTo($managerPermissions);
        }
        
        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            // Staff can only view
            $staffPermissions = array_filter($allPermissions, function($permission) {
                return str_starts_with($permission, 'view');
            });
            $staffRole->givePermissionTo($staffPermissions);
        }
        
        // Create inventory manager role
        $inventoryManagerRole = Role::firstOrCreate(['name' => 'inventory manager']);
        $inventoryManagerRole->givePermissionTo($allPermissions);
        
        // Create inventory staff role
        $inventoryStaffRole = Role::firstOrCreate(['name' => 'inventory staff']);
        $inventoryStaffPermissions = array_filter($allPermissions, function($permission) {
            return !str_contains($permission, 'delete') && 
                   !str_contains($permission, 'restore') && 
                   !str_contains($permission, 'force delete');
        });
        $inventoryStaffRole->givePermissionTo($inventoryStaffPermissions);
    }
}
