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
        'is_active',
        'organization_id',
        'languages'
    ];

    public array $translatable = [
        'title',
        'slug',
        'description',
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
            'languages' => 'array'
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
            ->distinct('email')
            ->count('email');
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
