<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IntegrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'integrations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('organization_id')
                    ->default(fn () => Filament::getTenant()?->id),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Disable this integration without deleting it.'),
                Select::make('type')
                    ->label('Integration Type')
                    ->options([
                        'webhook' => 'Webhook',
                        'mailchimp' => 'Mailchimp',
                    ])
                    ->required()
                    ->live(),
                Fieldset::make('Webhook Configuration')
                    ->visible(fn (Get $get) => $get('type') === 'webhook')
                    ->schema([
                        Group::make()
                            ->statePath('settings')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('endpoint_url')
                                    ->label('Endpoint URL')
                                    ->url()
                                    ->required(fn (Get $get) => $get('type') === 'webhook'),

                                TextInput::make('secret_token')
                                    ->label('Secret Token')
                                    ->helperText('Optional: We will send this in the headers as X-Voces-Signature.'),
                            ])
                            ->columns(2),
                    ]),
                Fieldset::make('Mailchimp Configuration')
                    ->visible(fn (Get $get) => $get('type') === 'mailchimp')
                    ->schema([
                        Group::make()
                            ->statePath('settings')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('api_key')
                                    ->password()
                                    ->label('API Key')
                                    ->required(fn (Get $get) => $get('type') === 'mailchimp'),
                                TextInput::make('list_id')
                                    ->label('Audience List ID')
                                    ->required(fn (Get $get) => $get('type') === 'mailchimp'),
                                Select::make('opt_in_field')
                                    ->label('Opt-In Checkbox')
                                    ->helperText('Select the checkbox field that users must check to be added to Mailchimp.')
                                    ->options(function () {
                                        return $this->getOwnerRecord()
                                            ->campaignFields()
                                            ->where('type', 'checkbox')
                                            ->pluck('label', 'name');
                                    })
                                    ->required(),
                                KeyValue::make('field_mappings')
                                    ->label('Field Mappings')
                                    ->helperText('Map Campaign fields to Mailchimp MERGE fields. For example: "email" => "EMAIL", "first_name" => "FNAME".')
                                    ->keyLabel('Voces Field')
                                    ->valueLabel('Mailchimp MERGE Field')
                                    ->default([
                                        "email" => "EMAIL",
                                        "first_name" => "FNAME",
                                        "last_name" => "LNAME"
                                    ])
                                    ->required(fn (Get $get) => $get('type') === 'mailchimp')
                                    ->rules([
                                        fn () => function (string $attribute, $value, \Closure $fail) {
                                            if (!is_array($value)) {
                                                return;
                                            }
                                            $hasEmail = collect($value)->contains(function ($item) {
                                                $mailchimpTag = strtoupper(trim($item['value'] ?? ''));
                                                return $mailchimpTag === 'EMAIL';
                                            });

                                            if (!$hasEmail) {
                                                $fail('You must map a Voces campaign field to the Mailchimp "EMAIL" merge tag.');
                                            }
                                        },
                                    ])
                                    ->columnSpanFull()
                            ])
                            ->columns(2),
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('organization_id', Filament::getTenant()->id))
            ->recordTitleAttribute('type')
            ->columns([
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'webhook' => 'info',
                        'mailchimp' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['organization_id'] = Filament::getTenant()->id;
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
    public function isReadOnly(): bool
    {
        return false;
    }
}
