<?php

namespace App\Policies;

use App\Models\Signature;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\Response;

class SignaturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Signature $signature)
    {
        if ($signature->organization_id === Filament::getTenant()?->id) {
            return true;
        }
        $campaign = $signature->campaign;

        if ($campaign->is_data_pooled && $campaign->organization_id === Filament::getTenant()?->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Signature $signature): bool
    {
        // Only allow updates if current tenant == signature's organization_id
        return $signature->organization_id === Filament::getTenant()?->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Signature $signature): bool
    {
        // Only allow deletion if current tenant == signature's organization_id
        return $signature->organization_id === Filament::getTenant()?->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Signature $signature): bool
    {
        // Only allow restoration if current tenant == signature's organization_id
        return $signature->organization_id === Filament::getTenant()?->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Signature $signature): bool
    {
        // Only allow force deletion if current tenant == signature's organization_id
        return $signature->organization_id === Filament::getTenant()?->id;
    }
}
