<?php
namespace App\Filament\Pages\Tenancy;

use App\Models\Organization;
use Filament\Forms;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register organization';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    protected function handleRegistration(array $data): Organization
    {
        $organization = Organization::create($data);

        $organization->users()->attach(auth()->user());

        return $organization;
    }
}
