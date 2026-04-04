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
                        TextInput::make('email')
                            ->email()
                            ->label('Signer Email')
                            ->helperText('Used for double opt-in verification and to prevent duplicate signatures.'),
                        Select::make('campaign_id')
                            ->relationship('campaign', 'title'),

                        DateTimePicker::make('signed_at'),

                        Toggle::make('is_verified')
                            ->label('Double Opt-In Verified'),
                    ])->columns(2),

                Section::make('Submitted Data')
                    ->schema([
                        // KeyValue is perfect for displaying flat JSON payloads cleanly
                        KeyValue::make('payload')
                            ->keyLabel('Field')
                            ->valueLabel('Submitted Value')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
