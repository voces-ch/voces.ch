<?php

namespace App\Models;

use App\Observers\SignatureObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

#[ObservedBy(SignatureObserver::class)]
class Signature extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'payload',
        'signed_at',
        'campaign_id',
        'origin',
        'organization_id',
        'unique_identifier',
        'verified_at',
        'verification_token',
        'token_expiration',
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
            'payload' => 'array',
            'verified_at' => 'timestamp',
            'verification_token' => 'string',
            'token_expiration' => 'datetime',
            'signed_at' => 'timestamp',
            'campaign_id' => 'integer',
            'organization_id' => 'integer',
            'unique_identifier' => 'string',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function duplicatedFrom(): BelongsTo
    {
        return $this->belongsTo(Signature::class, 'is_duplicate_of')
            ->withoutGlobalScopes();
    }

    public function isDuplicate(): bool
    {
        return $this->is_duplicate_of !== false;
    }

    public function source(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_duplicate_of) {

                    return $this->duplicatedFrom->organization;
                }
                return $this->organization;
            }
        );
    }

    public function isVerified(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->verified_at) {
                    return false;
                }

                if ($this->campaign->is_email_verification_enabled) {
                    return true;
                }

                return true;
            }
        );
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
