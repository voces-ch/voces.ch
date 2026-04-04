<?php
namespace App\Filament\Pages\Tenancy;

use Filament\Forms;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
                Forms\Components\TextInput::make('uuid')
                    ->disabled()
                    ->label('UUID'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                        if ($state === null || $state === $old) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('default_locale')
                    ->label('Default Language')
                    ->options([
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'it' => 'Italiano',
                        'en' => 'English',
                    ])
                    ->required(),
            ]);
    }
}
