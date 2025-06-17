<?php

namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseInvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view purchase invoices');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseInvoice $purchaseInvoice): bool
    {
        return $user->can('view purchase invoices');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create purchase invoices');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseInvoice $purchaseInvoice): bool
    {
        return $user->can('edit purchase invoices');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseInvoice $purchaseInvoice): bool
    {
        return $user->can('delete purchase invoices');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PurchaseInvoice $purchaseInvoice): bool
    {
        return $user->can('edit purchase invoices');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PurchaseInvoice $purchaseInvoice): bool
    {
        return $user->can('delete purchase invoices');
    }
}
