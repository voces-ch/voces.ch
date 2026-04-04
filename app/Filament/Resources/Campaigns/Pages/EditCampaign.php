<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            Action::make('manage_languages')
                ->label('Manage Languages')
                ->icon('heroicon-o-language')
                ->modalHeading('Manage Campaign Languages')
                ->modalDescription('Select all the languages this campaign should support.')
                ->form(function (Campaign $record) {
                    $allLocales = [
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'it' => 'Italiano',
                        'en' => 'English'
                    ];

                    return [
                        Select::make('languages')
                            ->label('Supported Languages')
                            ->multiple()
                            ->options($allLocales)
                            ->default($record->languages ?? [])
                            ->minItems(1)
                            ->required()
                    ];
                })
                ->action(function (array $data, Campaign $record, Action $action) {
                    $record->update([
                        'languages' => $data['languages']
                    ]);

                    Notification::make()
                        ->title('Languages updated')
                        ->body('Please make sure to translate all campaign content into the selected languages.')
                        ->success()
                        ->send();
                    $action->redirect($this->getResource()::getUrl('edit', ['record' => $record]));
                })
                ->visible(fn (Campaign $record) => $record->organization_id === Filament::getTenant()?->id),
        ];
    }
}
