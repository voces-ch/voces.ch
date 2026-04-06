<?php
namespace App\Filament\Pages\Tenancy;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use File;
use Illuminate\Support\Str;

class EditOrganizationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Organization Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->description('Update your organization\'s basic information. The UUID is used for integration with other organizations and cannot be changed.')
                    ->schema([
                    TextInput::make('uuid')
                        ->disabled()
                        ->copyable()
                        ->helperText('The UUID is used to identify your organization when other organizations want to add you as a campaign partner. It cannot be changed.')
                        ->label('UUID'),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                            if ($state === null || $state === $old) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->label('URL Slug')
                        ->helperText('The URL slug is used in the URL to access your organization\'s profile. It must be unique across all organizations and can only contain letters, numbers, and hyphens.')
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Select::make('default_locale')
                        ->label('Default Language')
                        ->options([
                            'de' => 'Deutsch',
                            'fr' => 'Français',
                            'it' => 'Italiano',
                            'en' => 'English',
                        ])
                        ->required(),
                    ])
                    ->columns(2),
                Section::make('Default Campaign Fields')
                    ->description('Define the default fields that will be included in every new campaign. These can be overridden at the campaign level.')
                    ->schema([
                    Repeater::make('default_campaign_fields')
                        ->schema([
                            TextInput::make('name')
                                ->label('Internal Key')
                                ->required()
                                ->helperText('e.g., first_name (No spaces)'),
                            Select::make('type')
                                ->options([
                                    'text' => 'Short Text (Single Line)',
                                    'textarea' => 'Long Text (Paragraph)',
                                    'email' => 'Email Address',
                                    'tel' => 'Phone Number',
                                    'number' => 'Number',
                                    'date' => 'Date',
                                    'checkbox' => 'Checkbox (e.g., Opt-in / Yes/No)',
                                ])
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('default_value', null))
                                ->required(),
                            // Using MarkdownEditor because RichEditor can't be filled programmatically [BUG]
                            // see https://github.com/filamentphp/filament/issues/17472
                            MarkdownEditor::make('label')
                                ->label('Public Label')
                                ->required()
                                ->helperText('e.g., First Name')
                                ->columnSpanFull()
                                ->translatable(supportedLocales: function() {
                                    $default_locale = Filament::getTenant()->default_locale ?? 'en';
                                    $localeNames = [
                                        'de' => 'Deutsch',
                                        'fr' => 'Français',
                                        'it' => 'Italiano',
                                        'en' => 'English',
                                    ];
                                    return [
                                        $default_locale => $localeNames[$default_locale] ?? $default_locale,
                                    ];
                                }),
                            TextInput::make('default_value_text')
                                ->label('Default Value')
                                ->visible(fn (Get $get) => in_array($get('type'), ['text', 'email', 'number', 'tel']))
                                ->formatStateUsing(fn (Get $get) => $get('default_value'))
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, $state) => $set('default_value', $state))
                                ->columnSpanFull()
                                ->dehydrated(false),

                            Textarea::make('default_value_textarea')
                                ->label('Default Value')
                                ->visible(fn (Get $get) => $get('type') === 'textarea')
                                ->formatStateUsing(fn (Get $get) => $get('default_value'))
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, $state) => $set('default_value', $state))
                                ->columnSpanFull()
                                ->dehydrated(false),

                            DatePicker::make('default_value_date')
                                ->label('Default Value')
                                ->visible(fn (Get $get) => $get('type') === 'date')
                                ->formatStateUsing(fn (Get $get) => $get('default_value'))
                                ->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('default_value', $state))
                                ->columnSpanFull()
                                ->dehydrated(false),

                            Toggle::make('default_value_checkbox')
                                ->label('Checked by default?')
                                ->visible(fn (Get $get) => $get('type') === 'checkbox')
                                ->formatStateUsing(fn (Get $get) => $get('default_value') === 'true' || $get('default_value') === '1')
                                ->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('default_value', $state ? 'true' : 'false'))
                                ->columnSpanFull()
                                ->dehydrated(false),
                            Toggle::make('is_required')
                                ->inline(false)
                                ->default(false),
                            Toggle::make('is_unique')
                                ->label('Use as Unique Identifier')
                                ->helperText('Prevents users from signing twice with the same value.'),
                            Hidden::make("default_value"),
                        ])
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
