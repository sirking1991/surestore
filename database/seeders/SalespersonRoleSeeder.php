<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SalespersonRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create salesperson role if it doesn't exist
        $salespersonRole = Role::firstOrCreate(['name' => 'salesperson']);
        
        // Assign existing permissions to the role
        $permissions = [
            'view content',
            'create content',
            'edit content',
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::where('name', $permission)->first();
            if ($perm) {
                $salespersonRole->givePermissionTo($perm);
            }
        }

        // Create salesperson users if they don't exist
        $salesperson1 = User::firstOrCreate(
            ['email' => 'john.sales@example.com'],
            [
                'first_name' => 'John',
                'last_name' => 'Sales',
                'password' => Hash::make('password'),
            ]
        );
        $salesperson1->assignRole('salesperson');

        $salesperson2 = User::firstOrCreate(
            ['email' => 'jane.seller@example.com'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Seller',
                'password' => Hash::make('password'),
            ]
        );
        $salesperson2->assignRole('salesperson');
    }
}
