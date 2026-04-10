<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(Lang::get('E-Mail-Adresse verifizieren'))
                ->line(Lang::get('Bitte klicke auf den untenstehenden Button, um deine E-Mail-Adresse zu verifizieren.'))
                ->action(Lang::get('E-Mail-Adresse verifizieren'), $url)
                ->line(Lang::get('Wenn du kein Konto erstellt hast, musst du nichts weiter tun.'));
        });
    }
}
