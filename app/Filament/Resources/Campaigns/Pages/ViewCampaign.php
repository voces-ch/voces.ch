<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\CampaignResource\Widgets\CampaignEmbedWidget;
use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_languages')
                ->label(__('Manage Languages'))
                ->icon('heroicon-o-language')
                ->modalHeading(__('Manage Campaign Languages'))
                ->modalDescription(__('Select all the languages this campaign should support.'))
                ->form(function (Campaign $record) {
                    $allLocales = [
                        'de' => 'Deutsch',
                        'fr' => 'Français',
                        'it' => 'Italiano',
                        'en' => 'English'
                    ];

                    return [
                        Select::make('languages')
                            ->label(__('Supported Languages'))
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
            EditAction::make()
                ->visible(fn (Campaign $record): bool => $record->organization_id === Filament::getTenant()?->id),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CampaignEmbedWidget::class,
        ];
    }
}
