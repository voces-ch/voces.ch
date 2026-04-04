<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CampaignForm
{
    protected static function getLocales(){
        $currentRecord = request()->route('record');
        $campaign = Campaign::find($currentRecord);
        if (!$campaign) {
            $tenant = Filament::getTenant();
            $defaultLocale = $tenant->default_locale ?? 'de';
            return array_map(fn ($locale) => $locale['native'], array_intersect_key([
                'de' => ['native' => 'Deutsch'],
                'fr' => ['native' => 'Français'],
                'it' => ['native' => 'Italiano'],
                'en' => ['native' => 'English'],
            ], array_flip([$defaultLocale])));
        }
        return array_map(fn ($locale) => $locale['native'], array_intersect_key([
            'de' => ['native' => 'Deutsch'],
            'fr' => ['native' => 'Français'],
            'it' => ['native' => 'Italiano'],
            'en' => ['native' => 'English'],
        ], array_flip($campaign->languages ?? [])));
    }
    protected static function getDefaultLocale(){
        $tenant = Filament::getTenant();
        return $tenant->default_locale;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->translatable(supportedLocales: self::getLocales()),
                TextInput::make('slug')
                    ->required()
                    ->translatable(supportedLocales: self::getLocales()),
                RichEditor::make('description')
                    ->columnSpanFull()
                    ->translatable(supportedLocales: self::getLocales()),

                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Repeater::make('campaignFields')
                    ->relationship()
                    ->schema([
                        TextInput::make('name')
                            ->label('Internal Key')
                            ->required()
                            ->helperText('e.g., first_name (No spaces)'),

                        TextInput::make('label')
                            ->label('Public Label')
                            ->required()
                            ->helperText('e.g., First Name')
                            ->translatable(supportedLocales: self::getLocales()),

                        Select::make('type')
                            ->options([
                                'text' => 'Short Text',
                                'email' => 'Email Address',
                                'number' => 'Number',
                                'date' => 'Date',
                                'boolean' => 'Checkbox (e.g., Opt-in)',
                            ])
                            ->required(),

                        Toggle::make('is_required')
                            ->inline(false)
                            ->default(false),
                    ])
                    ->default([
                        [
                            'name' => 'first_name',
                            'label' => [
                                'de' => 'Vorname',
                            ],
                            'type' => 'text',
                            'is_required' => true,
                        ],
                        [
                            'name' => 'last_name',
                            'label' => [
                                'de' => 'Nachname',
                            ],
                            'type' => 'text',
                            'is_required' => true,
                        ],
                        [
                            'name' => 'zip_code',
                            'label' => [
                                'de' => 'Postleitzahl',
                            ],
                            'type' => 'text',
                            'is_required' => true,
                        ],
                    ])
                    ->columns(4)
                    ->orderColumn('order') // Keeps your drag-and-drop sorting
                    ->reorderableWithButtons() // Adds handy up/down arrows for accessibility
                    ->addActionLabel('Add Custom Field')
                    ->columnSpanFull(),
                Hidden::make("languages")
                    ->default([self::getDefaultLocale()]),
                Hidden::make('organization_id')
                    ->default(fn () => Filament::getTenant()?->id)
                    ->required(),
            ]);
    }
}
