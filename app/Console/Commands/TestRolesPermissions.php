<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestRolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-roles-permissions {email? : The email of the user to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the roles and permissions system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Roles & Permissions System');
        $this->newLine();

        // Show all roles
        $this->info('Available Roles:');
        $roles = Role::all();
        $this->table(
            ['ID', 'Name', 'Guard', 'Permissions Count'],
            $roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions_count' => $role->permissions->count(),
            ])
        );

        // Show all permissions
        $this->info('Available Permissions:');
        $permissions = Permission::all();
        $this->table(
            ['ID', 'Name', 'Guard', 'Roles Count'],
            $permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
                'roles_count' => $permission->roles->count(),
            ])
        );

        // Test a specific user if provided
        $email = $this->argument('email');
        if ($email) {
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return 1;
            }
            
            $this->info("Testing user: {$user->getFilamentName()} ({$user->email})");
            
            // Show user roles
            $this->info('User Roles:');
            $this->table(
                ['Role'],
                $user->roles->map(fn ($role) => ['role' => $role->name])
            );
            
            // Show user permissions
            $this->info('User Permissions:');
            $this->table(
                ['Permission', 'Via Role'],
                $user->getAllPermissions()->map(function ($permission) use ($user) {
                    $viaRoles = $user->roles->filter(function ($role) use ($permission) {
                        return $role->permissions->contains('id', $permission->id);
                    })->pluck('name')->join(', ');
                    
                    return [
                        'permission' => $permission->name,
                        'via_role' => $viaRoles ?: 'Direct',
                    ];
                })
            );
            
            // Test some permissions
            $this->info('Permission Tests:');
            $testPermissions = [
                'view users',
                'create users',
                'edit users',
                'delete users',
                'view content',
                'create content',
                'edit content',
                'delete content',
                'manage settings',
            ];
            
            $results = [];
            foreach ($testPermissions as $permission) {
                $results[] = [
                    'permission' => $permission,
                    'has_permission' => $user->hasPermissionTo($permission) ? 'Yes' : 'No',
                ];
            }
            
            $this->table(['Permission', 'Has Permission'], $results);
        }

        $this->info('Roles and permissions test completed successfully.');
        return 0;
    }
}
