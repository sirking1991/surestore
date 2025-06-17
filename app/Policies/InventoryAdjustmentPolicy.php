<?php

namespace App\Policies;

use App\Models\InventoryAdjustment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventoryAdjustmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view inventory adjustments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('view inventory adjustments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create inventory adjustments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('edit inventory adjustments');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('delete inventory adjustments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('restore inventory adjustments');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('force delete inventory adjustments');
    }
    
    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $user->can('approve inventory adjustments');
    }
}
