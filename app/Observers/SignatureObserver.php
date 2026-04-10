<?php

namespace App\Observers;

use App\Models\Signature;

class SignatureObserver
{
    /**
     * Handle the Signature "created" event.
     */
    public function created(Signature $signature)
    {
        if ($signature->is_duplicate_of) {
            return;
        }

        $campaign = $signature->campaign;
        if ($signature->organization_id === $campaign->organization_id) {
            return;
        }

        if (! $campaign->is_data_pooled) {
            return;
        }

        $duplicate = $signature->replicate();
        $duplicate->organization_id = $campaign->organization_id;
        $duplicate->is_duplicate_of = $signature->id;
        $duplicate->saveQuietly();
    }

    /**
     * Handle the Signature "updated" event.
     */
    public function updated(Signature $signature): void
    {
        if ($signature->is_duplicate_of) {
            return;
        }

        $campaign = $signature->campaign;
        if ($signature->organization_id === $campaign->organization_id) {
            return;
        }

        if (! $campaign->is_data_pooled) {
            return;
        }

        $duplicate = Signature::where('is_duplicate_of', $signature->id)->first();
        if (! $duplicate) {
            return;
        }

        if ($signature->is_verified && ! $duplicate->is_verified) {
            $duplicate->verified_at = now();
            $duplicate->saveQuietly();
        }
    }

    /**
     * Handle the Signature "deleted" event.
     */
    public function deleted(Signature $signature): void
    {
        //
    }

    /**
     * Handle the Signature "restored" event.
     */
    public function restored(Signature $signature): void
    {
        //
    }

    /**
     * Handle the Signature "force deleted" event.
     */
    public function forceDeleted(Signature $signature): void
    {
        //
    }
}
