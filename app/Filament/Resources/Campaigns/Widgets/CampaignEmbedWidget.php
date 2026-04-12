<?php

namespace App\Filament\Resources\Campaigns\Widgets;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class CampaignEmbedWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.resources.campaigns.widgets.campaign-embed-widget';
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];

    protected static function getAllVersions(): array
    {
        // In a real application, you might fetch this from a database or configuration file
        return [
            '0.0.11' => __('Version 0.0.11'),
            'latest' => __('Latest Stable'),
        ];
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
                Select::make("version")
                    ->options(self::getAllVersions())
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
            ])
            ->columns(2)
            ->statePath('data');
    }
}
