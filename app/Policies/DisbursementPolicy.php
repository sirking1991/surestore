<?php

namespace App\Policies;

use App\Models\Disbursement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisbursementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view disbursements');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Disbursement $disbursement): bool
    {
        return $user->can('view disbursements');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create disbursements');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Disbursement $disbursement): bool
    {
        return $user->can('edit disbursements');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Disbursement $disbursement): bool
    {
        return $user->can('delete disbursements');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Disbursement $disbursement): bool
    {
        return $user->can('edit disbursements');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Disbursement $disbursement): bool
    {
        return $user->can('delete disbursements');
    }
}
