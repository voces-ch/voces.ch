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
                ->subject(__('Verify your email address'))
                ->line(__('First, a note about data security: Please make sure your password is secure and that you are not using it for other accounts. We also recommend enabling two-factor authentication to further protect your account (click on your profile in the top right after logging in and enable two-factor authentication). Please click the button below to verify your email address.'))
                ->action(__('Verify Email Address'), $url)
                ->line(__('If you did not create an account, no further action is required.'));
        });
    }
}
