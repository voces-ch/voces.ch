<?php

namespace App\Filament\Resources\CampaignPages\Schemas;

use App\Models\CampaignPage;
use Athphane\FilamentEditorjs\Forms\Components\EditorjsTextField;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Webbingbrasil\FilamentCopyActions\Actions\CopyAction;

class CampaignPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Page details'))
                    ->schema([
                        Toggle::make('is_published')
                            ->label(__('Published'))
                            ->default(false)
                            ->live()
                            ->disabled(fn (Get $get) => $get("campaign_id") === null)
                            ->afterStateUpdated(function (?CampaignPage $campaignPage, ?bool $state) {
                                if (!$campaignPage || $state === null || $state === $campaignPage->is_published) {
                                    return;
                                }
                                $campaignPage->is_published = $state;
                                $campaignPage->save();
                                Notification::make()
                                    ->title($state ? __('Campaign page published') : __('Campaign page unpublished'))
                                    ->body($state ? __('The campaign page is now visible to the public.') : __('The campaign page is now hidden from the public.'))
                                    ->success()
                                    ->send();
                            })
                            ->columnSpanFull()
                            ->helperText(__('Only published campaign pages are visible to the public.')),
                        Select::make("campaign_id")
                            ->label(__('Campaign'))
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
                            ->label(__('Language'))
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
                        Select::make('theme')
                            ->options([
                                'minimal' => 'Minimal',
                            ])
                            ->label(__('Theme'))
                            ->default('minimal')
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('heroine_image')
                            ->collection('heroine_image')
                            ->disk("public")
                            ->label(__('Heroine Image'))
                            ->required()
                            ->helperText(__('Upload a heroine image for this campaign page. This will be displayed at the top of the page. Recommended dimensions: 1200x600 pixels.')),
                        TextInput::make('slug')
                            ->required()
                            ->prefix(config('app.act_url') . '/')
                            ->suffixIcon(Heroicon::GlobeAlt)
                            ->label(__('Slug'))
                            ->hintAction(function(Get $get) {
                                $slug = $get('slug');
                                $isPublished = $get('is_published');
                                if (!$slug || !$isPublished) {
                                    return null;
                                }
                                $url = config('app.act_url') . '/' . $slug;
                                return CopyAction::make()
                                    ->copyable($url)
                                    ->successNotificationMessage(__('Page URL copied to clipboard'))
                                    ->label(__('Copy Page URL'));
;
                            })
                            ->columnSpanFull()
                            ->helperText(__('The slug is used to generate the URL for this campaign page. It must be unique across all campaign pages – even the ones you didn\'t create.')),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make(__('Page content'))
                    ->schema([
                        EditorjsTextField::make('content')
                            ->label(false)
                            ->required()
                            ->label(__('Content'))
                            ->placeholder('Start writing here or use / to insert blocks...')
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
