<?php

namespace App\Filament\Resources\CampaignResource\Widgets;

use App\Models\Campaign;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public function mount(): void
    {
        $this->form->fill([
            'language' => $this->record->languages[0] ?? 'de',
            'source' => 'organic',
            'theme' => 'minimal',
            'version' => 'latest',
            'origin' => null,
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
                    ->options(function() {
                        // Get all directories in public/widget/*
                        $directories = glob(public_path('widget/*'), GLOB_ONLYDIR);
                        // Extract the directory names and use them as options
                        $options = ["latest" => "Latest"];
                        foreach ($directories as $dir) {
                            $dirName = basename($dir);
                            $options[$dirName] = ucfirst($dirName);
                        }
                        return $options;
                    })
                    ->live(),
                Select::make('source')
                    ->options($sources)
                    ->live(),
                TextInput::make('origin')
                    ->label('Tracking Origin (Optional)')
                    ->helperText('e.g., homepage, facebook_ad, newsletter')
                    ->live()
                    ->maxLength(255),
            ])
            ->columns(2)
            ->statePath('data');
    }
}
