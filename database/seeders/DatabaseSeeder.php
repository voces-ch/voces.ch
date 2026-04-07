<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Organization; // Adjust this if your tenant model is named differently (e.g., Tenant, Workspace)
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $hostUser = User::firstOrCreate(
            ['email' => 'dev@voces.ch'],
            [
                'name' => 'Host User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $hostOrg = Organization::firstOrCreate(
            ['name' => 'Aktionskomitee Silbersee'],
            [
                "slug" => Str::slug('Aktionskomitee Silbersee'),
                "default_locale" => 'de',
            ]
        );

        if (! $hostUser->organizations()->where('organization_id', $hostOrg->id)->exists()) {
            $hostUser->organizations()->attach($hostOrg->id);
        }

        $partnerUser = User::firstOrCreate(
            ['email' => 'partner@voces.ch'],
            [
                'name' => 'Partner User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $partnerOrg = Organization::firstOrCreate(
            ['name' => 'Naturschutzverein Silbersee'],
            [
                "slug" => Str::slug('Naturschutzverein Silbersee'),
                "default_locale" => 'de',
            ]
        );

        if (! $partnerUser->organizations()->where('organization_id', $partnerOrg->id)->exists()) {
            $partnerUser->organizations()->attach($partnerOrg->id);
        }

        $campaign = Campaign::updateOrCreate(
            ['uuid' => 'c7439b13-cabe-4daf-8f21-1f1b2980edd6'],
            [
                'organization_id' => $hostOrg->id,
                'title' => [
                    "de" => 'Kein Beton am Silbersee',
                    "fr" => 'Pas de béton au Silbersee',
                ],
                'description' => [
                    "de" => '<p>Ein internationaler Investor plant den Bau eines gigantischen Einkaufszentrums direkt am naturbelassenen Nordufer des Silbersees. Wir müssen diesen ökologischen Wahnsinn stoppen!</p>',
                    "fr" => '<p>Un investisseur international envisage la construction d\'un énorme centre commercial directement sur la rive nord naturelle du lac Silbersee. Nous devons arrêter ce folie écologique !</p>'
                ],
                'submit_label' => [
                    "de" => 'Jetzt unterschreiben',
                    "fr" => 'Signez maintenant'
                ],
                'signature_goal' => 10000,
                "slug" => [
                    "de" => Str::slug('Kein Beton am Silbersee'),
                    "fr" => Str::slug('Pas de béton au Silbersee'),
                ],
                "success_type" => 'message',
                "success_message" => [
                    "de" => '<h1>Danke für deine Unterstützung!</h1><p>Gemeinsam können wir den Bau am Silbersee verhindern. Teile die Kampagne jetzt mit deinen Freund:innen und in den sozialen Medien, um noch mehr Menschen zu mobilisieren!</p>',
                    "fr" => '<h1>Merci pour votre soutien !</h1><p>Ensemble, nous pouvons empêcher la construction au Silbersee. Partagez la campagne dès maintenant avec vos amis et sur les réseaux sociaux pour mobiliser encore plus de personnes !</p>'
                ],
                "languages" => ['de', "fr"],
            ]
        );

        $campaign->campaignPartners()->firstOrCreate(
            ['organization_id' => $partnerOrg->id],
            [
                'source_slug' => 'naturschutz-silbersee',
            ]
        );

        $campaign->campaignFields()->delete();
        $campaign->campaignFields()->createMany([
            [
                'name' => 'first_name',
                'label' => [
                    "de" => 'Vorname',
                    "fr" => 'Prénom'
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'default_value' => '',
                'order' => 1
            ],
            [
                'name' => 'last_name',
                'label' => [
                    "de" => 'Nachname',
                    "fr" => 'Nom de famille'
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'default_value' => '',
                'order' => 2
            ],
            [
                'name' => 'email',
                'label' => [
                    "de" => 'E-Mail Adresse',
                    "fr" => 'Adresse e-mail'
                ],
                'type' => 'email',
                'is_required' => true,
                'is_unique' => true,
                'default_value' => '',
                'order' => 3
            ],
            [
                'name' => 'zip_code',
                'label' => [
                    "de" => 'Postleitzahl',
                    "fr" => 'Code postal'
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => false,
                'default_value' => '',
                'order' => 4
            ],
            [
                'name' => 'newsletter_opt_in',
                'label' => [
                    "de" => 'Ja, haltet mich auf dem Laufenden!',
                    "fr" => 'Oui, tenez-moi informé !'
                ],
                'type' => 'checkbox',
                'is_required' => false,
                'is_unique' => false,
                'default_value' => true,
                'order' => 5
            ],
        ]);

        $this->command->info('Seeding development environment and partnership with Signatures...');
        $this->call(SignatureSeeder::class);

        $this->command->info('Dev environment and partnerships seeded successfully! 🚀');
        $this->command->table(
            ['Role', 'Login', 'Password', 'Partner Source Slug'],
            [
                ['Host', 'dev@voces.ch', 'password', 'N/A'],
                ['Partner', 'partner@voces.ch', 'password', 'naturschutz-silbersee'],
            ]
        );
    }
}
