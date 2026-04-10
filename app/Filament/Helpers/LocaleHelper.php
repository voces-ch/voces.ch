<?php
namespace App\Filament\Helpers;

use App\Models\Campaign;
use Filament\Facades\Filament;

class LocaleHelper
{
    public static function getLocales(?Campaign $campaign = null)
    {
        if (!$campaign) {
            $tenant = Filament::getTenant();
            $defaultLocale = $tenant->default_locale ?? 'de';
            return array_map(fn ($locale) => $locale['native'], array_intersect_key([
                'de' => ['native' => 'Deutsch'],
                'fr' => ['native' => 'Français'],
                'it' => ['native' => 'Italiano'],
                'en' => ['native' => 'English'],
            ], array_flip([$defaultLocale])));
        }

        return array_map(fn ($locale) => $locale['native'], array_intersect_key([
            'de' => ['native' => 'Deutsch'],
            'fr' => ['native' => 'Français'],
            'it' => ['native' => 'Italiano'],
            'en' => ['native' => 'English'],
        ], array_flip($campaign->languages ?? [])));
    }

    public static function getDefaultLocale(){
        $tenant = Filament::getTenant();
        return $tenant->default_locale;
    }

    public static function getDefaultSuccessMessage(?Campaign $campaign = null)
    {
        $defaultLocale = self::getDefaultLocale();
        $messages = [
            'de' => '<h1>Vielen Dank für Ihre Unterstützung!</h1><p>Ihre Unterschrift wurde erfolgreich erfasst.</p>',
            'fr' => '<h1>Merci pour votre soutien !</h1><p>Votre signature a été enregistrée avec succès.</p>',
            'it' => '<h1>Grazie per il tuo supporto!</h1><p>La tua firma è stata registrata con successo.</p>',
            'en' => '<h1>Thank you for your support!</h1><p>Your signature has been successfully recorded.</p>',
        ];

        return $campaign ? ($campaign->success_message[$defaultLocale] ?? $messages[$defaultLocale]) : $messages[$defaultLocale];
    }

    public static function getDefaultVerificationSuccessMessage(?Campaign $campaign = null)
    {
        $defaultLocale = self::getDefaultLocale();
        $messages = [
            'de' => 'Deine E-Mail-Adresse wurde erfolgreich verifiziert und deine Unterschrift ist jetzt gültig!',
            'fr' => 'Votre adresse e-mail a été vérifiée avec succès et votre signature est maintenant valide !',
            'it' => 'Il tuo indirizzo email è stato verificato con successo e la tua firma è ora valida!',
            'en' => 'Your email address has been successfully verified and your signature is now valid!',
        ];

        return $campaign ? ($campaign->verification_success_message[$defaultLocale] ?? $messages[$defaultLocale]) : $messages[$defaultLocale];
    }

    public static function getDefaultSubmitButtonText(?Campaign $campaign = null)
    {
        $defaultLocale = self::getDefaultLocale();
        $texts = [
            'de' => 'Jetzt unterschreiben',
            'fr' => 'Signez maintenant',
            'it' => 'Firma ora',
            'en' => 'Sign Now',
        ];

        return $campaign ? ($campaign->submit_button_text[$defaultLocale] ?? $texts[$defaultLocale]) : $texts[$defaultLocale];
    }
}
