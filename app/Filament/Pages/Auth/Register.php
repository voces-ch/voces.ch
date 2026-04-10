<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Select::make('locale')
                    ->label(__('Sprache'))
                    ->options([
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'it' => 'Italiano',
                        'en' => 'English',
                    ])
                    ->default('de')
                    ->required(),
            ]);
    }
}
