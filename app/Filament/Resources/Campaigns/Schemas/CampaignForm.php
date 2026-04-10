<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Filament\Helpers\LocaleHelper;
use App\Filament\Schemas\Shared\CampaignFieldSchema;
use App\Models\Campaign;
use Closure;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class CampaignForm
{
    public static function configure(Schema $schema, ?Campaign $campaign = null): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->schema([
                    Toggle::make('is_active')
                        ->default(true)
                        ->columnSpanFull()
                        ->label(__('Active'))
                        ->required(),
                    TextInput::make('title')
                        ->required()
                        ->label(__('Title'))
                        ->translatable(supportedLocales: LocaleHelper::getLocales($campaign)),
                    TextInput::make('submit_label')
                        ->label(__('Submit Button Text'))
                        ->default(fn () => LocaleHelper::getDefaultSubmitButtonText($campaign))
                        ->translatable(supportedLocales: LocaleHelper::getLocales($campaign)),
                    TextInput::make('signature_goal')
                        ->label(__('Signature Goal'))
                        ->numeric()
                        ->minValue(1),
                    Toggle::make('is_data_pooled')
                        ->label(__('Pool Campaign Data'))
                        ->helperText(__('If enabled, you (the Host) will have access to all signee data collected by your Partners.'))
                        ->default(false)
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull()
                        ->hintActions([
                            Action::make('enable_pooling')
                                ->label(__('Enable Pooling'))
                                ->icon('heroicon-m-lock-open')
                                ->color('warning')
                                ->visible(fn (Get $get) => ! $get('is_data_pooled'))
                                ->requiresConfirmation()
                                ->modalHeading(__('Legal Responsibility Warning'))
                                ->modalDescription(__('By enabling this, you are breaking the data silo. It is your strict responsibility to ensure that supporters who sign through your partners\' forms are explicitly informed in their privacy policy that you (the Host) will also receive their data. Are you sure you want to proceed?'))
                                ->modalSubmitActionLabel(__('I Understand, Enable Pooling'))
                                ->action(function (Set $set) {
                                    $set('is_data_pooled', true);
                                }),

                            Action::make('disable_pooling')
                                ->label(__('Disable Pooling'))
                                ->icon('heroicon-m-lock-closed')
                                ->color('danger')
                                ->visible(fn (Get $get) => $get('is_data_pooled'))
                                ->action(function (Set $set) {
                                    $set('is_data_pooled', false);
                                }),

                        ])
                    ])
                    ->columns(2)
                    ->description(__('Configure the basic information for your campaign, such as the title, description, and submit button text.'))
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make(__('Success Logic'))
                    ->description(__('Configure what happens after someone successfully signs up for your campaign.'))
                    ->schema([
                        Select::make('success_type')
                            ->label(__('Success Action'))
                            ->options([
                                'message' => 'Show a Success Message',
                                'redirect' => 'Redirect to a URL'
                            ])
                            ->default('message')
                            ->live(),
                        RichEditor::make('success_message')
                            ->label(__('Success Message'))
                            ->default(LocaleHelper::getDefaultSuccessMessage($campaign))
                            ->translatable(supportedLocales: LocaleHelper::getLocales($campaign))
                            ->visible(fn (Get $get) => $get('success_type') === 'message'),
                        TextInput::make('success_url')
                            ->url()
                            ->label(__('Success URL'))
                            ->helperText(__('Enter the full URL, including https://'))
                            ->translatable(supportedLocales: LocaleHelper::getLocales($campaign))
                            ->visible(fn (Get $get) => $get('success_type') === 'redirect')
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Section::make(__('Custom Campaign Fields'))
                    ->description(__('Add custom fields to your campaign form. The "Unique Identifier" field will be used to prevent duplicate submissions and must be of type "text" or "email".'))
                    ->schema([
                    Repeater::make('campaignFields')
                        ->label(__('Campaign Fields'))
                        ->relationship()
                        ->schema(CampaignFieldSchema::getFields($campaign))
                        ->default(Filament::getTenant()->default_campaign_fields ?? [])
                        ->columns(2)
                        ->orderColumn('order')
                        ->reorderableWithButtons()
                        ->addActionLabel(__('Add Field'))
                        ->rule(function () {
                            return function (string $attribute, mixed $value, Closure $fail) {
                                $uniqueCount = collect($value)->where('is_unique', true)->count();
                                if ($uniqueCount > 1) {
                                    $fail(__('Only one field can be marked as the unique identifier.'));
                                }
                                if ($uniqueCount === 0) {
                                    $fail(__('You must select exactly one field to be the unique identifier.'));
                                }

                                $uniqueField = collect($value)->firstWhere('is_unique', true);
                                if ($uniqueField && !in_array($uniqueField['type'], ['text', 'email'])) {
                                    $fail(__('The unique identifier field must be of type "text" or "email".'));
                                }

                                if ($uniqueField && !$uniqueField['is_required']) {
                                    $fail(__('The unique identifier field must be marked as required.'));
                                }

                                $fieldNames = collect($value)->pluck('name');
                                if ($fieldNames->count() !== $fieldNames->unique()->count()) {
                                    $fail(__('Each field must have a unique internal key.'));
                                }
                            };
                        })
                        ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->collapsible()
                    ->columnSpanFull(),
                Hidden::make("languages")
                    ->default([LocaleHelper::getDefaultLocale()]),
                Hidden::make('organization_id')
                    ->default(fn () => Filament::getTenant()?->id)
                    ->required(),
            ]);
    }
}
