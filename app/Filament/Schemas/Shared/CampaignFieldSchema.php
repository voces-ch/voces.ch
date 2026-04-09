<?php
namespace App\Filament\Schemas\Shared;

use App\Filament\Helpers\LocaleHelper;
use App\Models\Campaign;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class CampaignFieldSchema
{
    public static function getFields(?Campaign $campaign = null): array
    {
        return [
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
                ->translatable(supportedLocales: LocaleHelper::getLocales($campaign)),
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
            Hidden::make("default_value"),
            Toggle::make('is_required')
                ->inline(false)
                ->default(false),
            Toggle::make('is_unique')
                ->label('Use as Unique Identifier')
                ->helperText('Prevents users from signing twice with the same value.'),
            Select::make('target_organization_ids')
                ->label('Display on which forms?')
                ->visible(fn (Get $get) => ! $get('is_required') && ! $get('is_unique'))
                ->multiple()
                ->options(function () use ($campaign) {
                    if (! $campaign) {
                        return [];
                    }
                    $partners = $campaign->campaignPartners()->pluck('source_slug', 'id');
                    return ['host' => 'Campaign host'] + $partners->toArray();
                })
                ->placeholder('All Partners (Leave blank for global fields)')
                ->helperText('If left blank, this field appears on the main form and EVERY partner form.')
                ->columnSpanFull(),
        ];
    }
}
