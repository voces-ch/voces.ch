<?php

namespace App\Filament\Resources\Campaigns\Widgets;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CampaignEmbedWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.resources.campaigns.widgets.campaign-embed-widget';
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];

    protected static function getAllVersions(): array
    {
        $cachedVersions = Cache::get('voces_widget_versions', ['latest' => __('Latest Stable Version')]);

        try {
            $response = Http::timeout(5)->get('https://api.github.com/repos/voces-ch/voces-widget/contents?ref=cdn');

            if (! $response->successful()) {
                return $cachedVersions;
            }

            $contents = collect($response->json())->filter(fn ($item) => $item['type'] === 'dir');
            $versions = $contents->mapWithKeys(fn ($content) => [$content['name'] => __("Version :version", ['version' => $content['name']])])->toArray();
            $versions['latest'] = __('Latest Stable Version');

            Cache::put('voces_widget_versions', $versions, now()->addHours(1));

            return $versions;
        } catch (\Throwable) {
            return $cachedVersions;
        }
    }


    protected static function getNewestVersionedWidget(): ?string
    {
        return array_key_first(array_slice(self::getAllVersions(), 0, 1));
    }

    public function mount(): void
    {
        // check if the current tenant is the host or a partner and set the source accordingly
        $defaultSource = Filament::getTenant()?->id === $this->record->organization_id ? 'organic' : $this->record->campaignPartners()->where('organization_id', Filament::getTenant()?->id)->value('source_slug') ?? 'organic';
        $this->form->fill([
            'language' => $this->record->languages[0] ?? 'de',
            'source' => $defaultSource,
            'theme' => 'minimal',
            'version' => self::getNewestVersionedWidget(),
            'origin' => null,
            'showProgress' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $locales = ['de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English'];
        $availableLocales = array_intersect_key($locales, array_flip($this->record->languages ?? ['de']));

        $partners = $this->record->campaignPartners()->pluck('source_slug', 'source_slug')->toArray();
        $sources = array_merge(['organic' => 'Organic (No Partner)'], $partners);

        return $schema
            ->schema([
                Select::make('language')
                    ->options($availableLocales)
                    ->live(),
                Select::make('theme')
                    ->options([
                        "minimal" => "Minimal",
                        "card" => "Card",
                        "impact" => "Impact",
                        "neon" => "Neon",
                        "registry" => "Registry",
                        "unstyled" => "Unstyled",
                    ])
                    ->default('minimal')
                    ->live(),
                Select::make('source')
                    ->options($sources)
                    // Only visible to host organization, hide for partners
                    ->visible(Filament::getTenant()?->id === $this->record->organization_id)
                    ->live(),
                TextInput::make('origin')
                    ->label(__('Tracking Origin (Optional)'))
                    ->helperText(__('e.g., homepage, facebook_ad, newsletter'))
                    ->live()
                    ->maxLength(255),
                Select::make("version")
                    ->options(self::getAllVersions())
                    ->label(__('Widget Version'))
                    ->helperText(__('Select the version of the widget to use. Using "Latest Stable Version" will automatically use the newest stable version available but may cause unexpected issues if a new version is released. Newly released version usually appear in the list within an hour.'))
                    ->columnSpanFull()
                    ->live(),
            ])
            ->columns(2)
            ->statePath('data');
    }
}
