<?php
namespace App\Models;

use Athphane\FilamentEditorjs\Traits\ModelHasEditorJsComponent;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CampaignPage extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, ModelHasEditorJsComponent;
    protected $fillable = [
        'campaign_id',
        'locale',
        'slug',
        'content',
        'theme',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'content' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
