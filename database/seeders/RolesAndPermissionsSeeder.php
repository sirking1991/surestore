<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // Content management
            'view content',
            'create content',
            'edit content',
            'delete content',
            
            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager role
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'view permissions',
            'view content',
            'create content',
            'edit content',
            'delete content',
            'manage settings',
        ]);

        // Staff role
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view content',
            'create content',
            'edit content',
        ]);

        // Salesperson role
        $salespersonRole = Role::create(['name' => 'salesperson']);
        $salespersonRole->givePermissionTo([
            'view content',
            'create content',
            'edit content',
        ]);

        // Create admin user
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::create([
            'first_name' => 'Manager',
            'last_name' => 'User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        // Create staff user
        $staff = User::create([
            'first_name' => 'Staff',
            'last_name' => 'User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
        ]);
        $staff->assignRole('staff');

        // Create salesperson users
        $salesperson1 = User::create([
            'first_name' => 'John',
            'last_name' => 'Sales',
            'email' => 'john.sales@example.com',
            'password' => Hash::make('password'),
        ]);
        $salesperson1->assignRole('salesperson');

        $salesperson2 = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Seller',
            'email' => 'jane.seller@example.com',
            'password' => Hash::make('password'),
        ]);
        $salesperson2->assignRole('salesperson');
    }
}
