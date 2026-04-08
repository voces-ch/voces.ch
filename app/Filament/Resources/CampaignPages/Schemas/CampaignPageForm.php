<?php

namespace App\Filament\Resources\CampaignPages\Schemas;

use Athphane\FilamentEditorjs\Forms\Components\EditorjsTextField;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CampaignPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page details')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false)
                            ->columnSpanFull()
                            ->helperText('Only published campaign pages are visible to the public.'),
                        Select::make("campaign_id")
                            ->label('Campaign')
                            ->options(Filament::getTenant()->campaigns()->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                                if ($state === null || $state === $old) {
                                    return;
                                }
                                $campaign = \App\Models\Campaign::find($state);
                                $slug = Str::slug($campaign->title);
                                $set('slug', $slug);
                            })
                            ->required(),
                        Select::make('locale')
                            ->options(function(Get $get) {
                                $campaignId = $get('campaign_id');
                                if (!$campaignId) {
                                    return [];
                                }
                                $campaign = \App\Models\Campaign::find($campaignId);
                                $possibleLocales = [
                                    "de" => 'Deutsch',
                                    "en" => 'English',
                                    "fr" => 'Français',
                                    "it" => 'Italiano',
                                ];
                                $supportertedLocales = $campaign->languages;
                                $options = [];
                                foreach ($supportertedLocales as $locale) {
                                    $options[$locale] = $possibleLocales[$locale] ?? $locale;
                                }
                                return $options;
                            })
                            ->required(),
                        TextInput::make('slug')
                            ->required()
                            ->helperText('The slug is used to generate the URL for this campaign page. It must be unique across all campaign pages – even the ones you didn\'t create.'),
                        Select::make('theme')
                            ->options([
                                'minimal' => 'Minimal',
                                'impact' => 'Impact',
                            ])
                            ->default('minimal')
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make('Page content')
                    ->schema([
                        EditorjsTextField::make('content')
                            ->label(false)
                            ->required()
                            ->placeholder('Start writing here or use / to insert blocks...')
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
