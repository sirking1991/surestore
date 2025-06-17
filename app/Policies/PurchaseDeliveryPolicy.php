<?php

namespace App\Policies;

use App\Models\PurchaseDelivery;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseDeliveryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view purchase deliveries');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseDelivery $purchaseDelivery): bool
    {
        return $user->can('view purchase deliveries');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create purchase deliveries');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseDelivery $purchaseDelivery): bool
    {
        return $user->can('edit purchase deliveries');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseDelivery $purchaseDelivery): bool
    {
        return $user->can('delete purchase deliveries');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PurchaseDelivery $purchaseDelivery): bool
    {
        return $user->can('edit purchase deliveries');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PurchaseDelivery $purchaseDelivery): bool
    {
        return $user->can('delete purchase deliveries');
    }
}
