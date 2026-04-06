<?php

namespace App\Observers;

use App\Models\Organization;

class OrganizationObserver
{
    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        $default_campaign_fields = [
            [
                'name' => "fist_name",
                'label' => [
                    "de" => "Vorname",
                    "fr" => "Prénom",
                    "it" => "Nome",
                    "en" => "First Name"
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => false,
            ],
            [
                'name' => "last_name",
                'label' => [
                    "de" => "Nachname",
                    "fr" => "Nom de famille",
                    "it" => "Cognome",
                    "en" => "Last Name"
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => false,
            ],
            [
                'name' => "email",
                'label' => [
                    "de" => "E-Mail",
                    "fr" => "E-Mail",
                    "it" => "E-Mail",
                    "en" => "E-Mail"
                ],
                'type' => 'email',
                'is_required' => true,
                'is_unique' => true,
            ],
            [
                'name' => "zip_code",
                'label' => [
                    "de" => "Postleitzahl",
                    "fr" => "Code postal",
                    "it" => "CAP",
                    "en" => "Zip Code"
                ],
                'type' => 'text',
                'is_required' => false,
                'is_unique' => false,
            ]
        ];
        $organization->update([
            'default_campaign_fields' => $default_campaign_fields,
        ]);
    }

    /**
     * Handle the Organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "deleted" event.
     */
    public function deleted(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "restored" event.
     */
    public function restored(Organization $organization): void
    {
        //
    }

    /**
     * Handle the Organization "force deleted" event.
     */
    public function forceDeleted(Organization $organization): void
    {
        //
    }
}
