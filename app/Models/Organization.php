<?php

namespace App\Models;

use App\Observers\OrganizationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use Spatie\Translatable\HasTranslations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([OrganizationObserver::class])]
class Organization extends Model implements HasMedia
{
    use HasFactory, HasUuids, HasTranslations, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'default_campaign_fields',
        'default_campaign_languages',
        'default_campaign_submit_label',
        'default_campaign_success_action',
        'default_campaign_success_message',
        'default_locale',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'uuid' => 'string',
            'default_campaign_fields' => 'array',
            'default_campaign_languages' => 'array',
            'default_campaign_submit_label' => 'array',
            'default_campaign_success_message' => 'array',
        ];
    }

    public array $translatable = [
        'default_campaign_submit_label',
        'default_campaign_success_message',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
