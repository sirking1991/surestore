<?php

namespace App\Policies;

use App\Models\StorageLocation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorageLocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view storage_locations');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StorageLocation $storageLocation): bool
    {
        return $user->hasPermissionTo('view storage_locations');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create storage_locations');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StorageLocation $storageLocation): bool
    {
        return $user->hasPermissionTo('edit storage_locations');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StorageLocation $storageLocation): bool
    {
        return $user->hasPermissionTo('delete storage_locations');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StorageLocation $storageLocation): bool
    {
        return $user->hasPermissionTo('edit storage_locations');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StorageLocation $storageLocation): bool
    {
        return $user->hasPermissionTo('delete storage_locations');
    }
}
