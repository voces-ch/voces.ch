<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;



class User extends Authenticatable implements FilamentUser, HasTenants, MustVerifyEmail, HasLocalePreference, HasAppAuthentication
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, InteractsWithAppAuthentication;

    protected $fillable = [
        'name',
        'email',
        'locale',
        'password',
        'organization_id',
        'email_verified_at',
        'google_id',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google_id' => 'string',
        ];
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // TODO: Implement some form of role-based access control here.
        return true;
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->organizations;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->organizations()->whereKey($tenant)->exists();
    }

    public function preferredLocale()
    {
        return $this->locale ?? 'de';
    }
}
