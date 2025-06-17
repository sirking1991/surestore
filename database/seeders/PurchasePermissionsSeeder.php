<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PurchasePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create purchase-related permissions
        $permissions = [
            // Purchase Order permissions
            'view purchase orders',
            'create purchase orders',
            'edit purchase orders',
            'delete purchase orders',
            
            // Purchase Delivery permissions
            'view purchase deliveries',
            'create purchase deliveries',
            'edit purchase deliveries',
            'delete purchase deliveries',
            
            // Purchase Invoice permissions
            'view purchase invoices',
            'create purchase invoices',
            'edit purchase invoices',
            'delete purchase invoices',
            
            // Disbursement permissions
            'view disbursements',
            'create disbursements',
            'edit disbursements',
            'delete disbursements',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to existing roles
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'view purchase orders',
                'create purchase orders',
                'edit purchase orders',
                'delete purchase orders',
                'view purchase deliveries',
                'create purchase deliveries',
                'edit purchase deliveries',
                'delete purchase deliveries',
                'view purchase invoices',
                'create purchase invoices',
                'edit purchase invoices',
                'delete purchase invoices',
                'view disbursements',
                'create disbursements',
                'edit disbursements',
                'delete disbursements',
            ]);
        }

        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            $staffRole->givePermissionTo([
                'view purchase orders',
                'view purchase deliveries',
                'view purchase invoices',
                'view disbursements',
            ]);
        }
    }
}
