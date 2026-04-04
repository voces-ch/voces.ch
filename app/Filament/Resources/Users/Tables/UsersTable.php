<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('invite_member')
                    ->label('Invite Team Member')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->helperText('If they already have a Voces account, they will be added instantly.'),

                        TextInput::make('name')
                            ->required()
                            ->helperText('Only used if we need to create a new account for them.'),
                    ])
                    ->action(function (array $data, \Livewire\Component $livewire) {
                        $tenant = Filament::getTenant();

                        // 1. Find or Create the User globally
                        $user = User::firstOrCreate(
                            ['email' => $data['email']],
                            [
                                'name' => $data['name'],
                                'password' => Hash::make(Str::random(24)), // Secure random password
                            ]
                        );

                        // 2. Check if they are already attached to this specific organization
                        if ($tenant->users()->where('user_id', $user->id)->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('User is already in this team.')
                                ->warning()
                                ->send();
                            return;
                        }

                        // 3. Attach them via the belongsToMany pivot!
                        $tenant->users()->attach($user->id);

                        // (Optional: Dispatch a Laravel Event here to email them a welcome/password reset link)

                        \Filament\Notifications\Notification::make()
                            ->title('Team member added successfully!')
                            ->success()
                            ->send();
                    })
            ])
            ->recordActions([
                Action::make('remove')
                    ->label('Remove from Team')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Remove Team Member')
                    ->modalDescription('This will revoke their access to this organization. It will not delete their global Voces account.')
                    // Prevent users from removing themselves to avoid locking out the organization
                    ->hidden(fn (User $record) => $record->id === auth()->id())
                    ->action(function (User $record) {
                        Filament::getTenant()->users()->detach($record->id);

                        \Filament\Notifications\Notification::make()
                            ->title('User removed from team.')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
