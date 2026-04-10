<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Spatie\Translatable\HasTranslations;

class Campaign extends Model
{
    use HasFactory, SoftDeletes, HasUuids, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'uuid',
        'description',
        'submit_label',
        'signature_goal',
        'is_active',
        'organization_id',
        'is_data_pooled',
        'languages',
        'success_type',
        'success_message',
        'success_url',
        'is_email_verification_enabled',
        'email_verification_field',
        'verification_success_action',
        'verification_success_message',
        'verification_success_url',
    ];

    public array $translatable = [
        'title',
        'slug',
        'description',
        'submit_label',
        'success_message',
        'success_url',
        'verification_success_message',
        'verification_success_url',
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
            'is_active' => 'boolean',
            'organization_id' => 'integer',
            'is_data_pooled' => 'boolean',
            'languages' => 'array',
            'is_email_verification_enabled' => 'boolean',
            'email_verification_field' => 'string',
             'verification_success_action' => 'string',
             'verification_success_message' => 'array',
             'verification_success_url' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function campaignPartners(): HasMany
    {
        return $this->hasMany(CampaignPartner::class);
    }

    public function campaignFields(): HasMany
    {
        return $this->hasMany(CampaignField::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class);
    }

    public function totalSignatures(): int
    {
        return $this->signatures()
            // ->where('is_verified', true) // TODO: Only count verified signatures once we have email verification in place
            ->distinct('unique_identifier')
            ->count('unique_identifier');
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }

    public function pages()
    {
        return $this->hasMany(CampaignPage::class);
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
