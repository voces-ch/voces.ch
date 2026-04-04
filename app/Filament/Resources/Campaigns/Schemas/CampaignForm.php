<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CampaignForm
{
    protected static function getLocales(?Campaign $campaign = null)
    {
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

    protected static function getDefaultCampaignFields(){
        $defaultLocale = self::getDefaultLocale();
        $labels = [
            'first_name' => [
                "de" => 'Vorname',
                "fr" => 'Prénom',
                "it" => 'Nome',
                "en" => 'First Name',
            ],
            'last_name' => [
                "de" => 'Nachname',
                "fr" => 'Nom de famille',
                "it" => 'Cognome',
                "en" => 'Last Name',
            ],
            'email' => [
                "de" => 'E-Mail Adresse',
                "fr" => 'Adresse e-mail',
                "it" => 'Indirizzo email',
                "en" => 'Email Address',
            ],
            'zip_code' => [
                "de" => 'Postleitzahl',
                "fr" => 'Code postal',
                "it" => 'CAP',
                "en" => 'Zip Code',
            ],
        ];
        return array_map(function ($name) use ($labels, $defaultLocale) {
            return [
                'name' => $name,
                'label' => [
                    $defaultLocale => $labels[$name][$defaultLocale] ?? Str::title(str_replace('_', ' ', $name)),
                ],
                'type' => 'text',
                'is_required' => true,
                'is_unique' => $name === 'email'
            ];
        }, ['first_name', 'last_name', 'email', 'zip_code']);
    }

    protected static function getDefaultSuccessMessage(?Campaign $campaign = null)
    {
        $defaultLocale = self::getDefaultLocale();
        $messages = [
            'de' => '<h1>Vielen Dank für Ihre Unterstützung!</h1><p>Ihre Unterschrift wurde erfolgreich erfasst.</p>',
            'fr' => '<h1>Merci pour votre soutien !</h1><p>Votre signature a été enregistrée avec succès.</p>',
            'it' => '<h1>Grazie per il tuo supporto!</h1><p>La tua firma è stata registrata con successo.</p>',
            'en' => '<h1>Thank you for your support!</h1><p>Your signature has been successfully recorded.</p>',
        ];

        return $campaign ? ($campaign->success_message[$defaultLocale] ?? $messages[$defaultLocale]) : $messages[$defaultLocale];
    }

    protected static function getDefaultSubmitButtonText(?Campaign $campaign = null)
    {
        $defaultLocale = self::getDefaultLocale();
        $texts = [
            'de' => 'Jetzt unterschreiben',
            'fr' => 'Signez maintenant',
            'it' => 'Firma ora',
            'en' => 'Sign Now',
        ];

        return $campaign ? ($campaign->submit_button_text[$defaultLocale] ?? $texts[$defaultLocale]) : $texts[$defaultLocale];
    }

    public static function configure(Schema $schema, ?Campaign $campaign = null): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                    Toggle::make('is_active')
                        ->default(true)
                        ->columnSpanFull()
                        ->required(),
                    TextInput::make('title')
                        ->required()
                        ->translatable(supportedLocales: self::getLocales($campaign)),
                    TextInput::make('slug')
                        ->required()
                        ->translatable(supportedLocales: self::getLocales($campaign)),
                    RichEditor::make('description')
                        ->columnSpanFull()
                        ->translatable(supportedLocales: self::getLocales($campaign)),
                    TextInput::make('submit_label')
                        ->label('Submit Button Text')
                        ->default(fn () => self::getDefaultSubmitButtonText($campaign))
                        ->translatable(supportedLocales: self::getLocales($campaign)),
                    TextInput::make('signature_goal')
                        ->label('Signature Goal')
                        ->numeric()
                        ->minValue(1),
                    ])
                    ->description('Configure the basic information for your campaign, such as the title, description, and submit button text.')
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make('Success Logic')
                    ->description('Configure what happens after someone successfully signs up for your campaign.')
                    ->schema([
                        Select::make('success_type')
                            ->label('Success Action')
                            ->options([
                                'message' => 'Show a Success Message',
                                'redirect' => 'Redirect to a URL'
                            ])
                            ->default('message')
                            ->live(),
                        RichEditor::make('success_message')
                            ->label('Success Message')
                            ->default(self::getDefaultSuccessMessage($campaign))
                            ->translatable(supportedLocales: self::getLocales($campaign))
                            ->visible(fn (Get $get) => $get('success_type') === 'message'),
                        TextInput::make('success_url')
                            ->url()
                            ->label('Success URL')
                            ->helperText('Enter the full URL, including https://')
                            ->translatable(supportedLocales: self::getLocales($campaign))
                            ->visible(fn (Get $get) => $get('success_type') === 'redirect')
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make('Custom Sign-Up Fields')
                    ->description('Add custom fields to your sign-up form. The "Unique Identifier" field will be used to prevent duplicate sign-ups and must be of type "text" or "email".')
                    ->schema([
                    Repeater::make('campaignFields')
                        ->relationship()
                        ->schema([
                            TextInput::make('label')
                                ->label('Public Label')
                                ->required()
                                ->helperText('e.g., First Name')
                                ->columnSpanFull()
                                ->translatable(supportedLocales: self::getLocales($campaign)),
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
                        ->default(self::getDefaultCampaignFields())
                        ->columns(2)
                        ->orderColumn('order')
                        ->reorderableWithButtons()
                        ->addActionLabel('Add Custom Field')
                        ->rule(function () {
                            return function (string $attribute, mixed $value, Closure $fail) {
                                $uniqueCount = collect($value)->where('is_unique', true)->count();
                                if ($uniqueCount > 1) {
                                    $fail('Only one field can be marked as the unique identifier.');
                                }
                                if ($uniqueCount === 0) {
                                    $fail('You must select exactly one field to be the unique identifier.');
                                }

                                $uniqueField = collect($value)->firstWhere('is_unique', true);
                                if ($uniqueField && !in_array($uniqueField['type'], ['text', 'email'])) {
                                    $fail('The unique identifier field must be of type "text" or "email".');
                                }

                                if ($uniqueField && !$uniqueField['is_required']) {
                                    $fail('The unique identifier field must be marked as required.');
                                }

                                $fieldNames = collect($value)->pluck('name');
                                if ($fieldNames->count() !== $fieldNames->unique()->count()) {
                                    $fail('Each field must have a unique internal key.');
                                }
                            };
                        })
                        ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->columnSpanFull(),
                Hidden::make("languages")
                    ->default([self::getDefaultLocale()]),
                Hidden::make('organization_id')
                    ->default(fn () => Filament::getTenant()?->id)
                    ->required(),
            ]);
    }
}
