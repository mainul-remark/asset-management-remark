<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the asset can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the asset can view the model.
     */
    public function view(User $user, Asset $model): bool
    {
        return true;
    }

    /**
     * Determine whether the asset can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the asset can update the model.
     */
    public function update(User $user, Asset $model): bool
    {
        return true;
    }

    /**
     * Determine whether the asset can delete the model.
     */
    public function delete(User $user, Asset $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the asset can restore the model.
     */
    public function restore(User $user, Asset $model): bool
    {
        return false;
    }

    /**
     * Determine whether the asset can permanently delete the model.
     */
    public function forceDelete(User $user, Asset $model): bool
    {
        return false;
    }
}
