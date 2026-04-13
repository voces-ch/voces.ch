<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    protected function allowedEmails(): array
    {
        return collect(explode(',', (string) config('horizon.allowed_emails', '')))
            ->map(fn (string $email) => trim($email))
            ->filter()
            ->values()
            ->all();
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        $allowedEmails = $this->allowedEmails();
        foreach ($allowedEmails as $email) {
            Horizon::routeMailNotificationsTo($email);
        }
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            return isset($user?->email)
                && in_array($user->email, $this->allowedEmails(), true);
        });
    }
}
