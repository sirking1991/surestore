<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateFirstAdminUser extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Create the admin user
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@surestore.online',
            'password' => Hash::make('q1w2e3Q!W@E#'),
            'email_verified_at' => now(),
        ]);
        
        // Assign admin role to the user
        $user->assignRole($adminRole);
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // Find and delete the admin user
        $user = User::where('email', 'admin@surestore.online')->first();
        
        if ($user) {
            $user->roles()->detach();
            $user->delete();
        }
    }
}
