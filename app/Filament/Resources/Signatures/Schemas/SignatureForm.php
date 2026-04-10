<?php

namespace App\Filament\Resources\Signatures\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SignatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Signature Details')
                    ->schema([
                        Select::make('campaign_id')
                            ->disabled()
                            ->relationship('campaign', 'title'),

                        DateTimePicker::make('signed_at')
                            ->disabled(),

                        Toggle::make('is_verified')
                            ->disabled()
                            ->label(__('Double Opt-In Verified')),
                    ])->columns(2),

                Section::make('Submitted Data')
                    ->schema([
                        KeyValue::make('payload')
                            ->keyLabel('Field')
                            ->valueLabel('Submitted Value')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
