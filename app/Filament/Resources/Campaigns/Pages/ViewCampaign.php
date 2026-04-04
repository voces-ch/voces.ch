<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('add_language')
                ->label('Add Language')
                ->icon('heroicon-o-language')
                ->form(function (Campaign $record) {
                    // Find which languages are NOT yet added
                    $allLocales = ['de' => 'Deutsch', 'fr' => 'Français', 'it' => 'Italiano', 'en' => 'English'];
                    $currentLocales = $record->languages ?? [];
                    $availableLocales = array_diff_key($allLocales, array_flip($currentLocales));

                    return [
                        Select::make('new_language')
                            ->label('Select Language')
                            ->options($availableLocales)
                            ->required()
                    ];
                })
                ->action(function (array $data, Campaign $record, Action $action) {
                    $languages = $record->languages ?? [];

                    if (!in_array($data['new_language'], $languages)) {
                        $languages[] = $data['new_language'];
                        $record->update(['languages' => $languages]);
                    }
                })
                ->visible(fn (Campaign $record) => count($record->languages ?? []) < 4 && $record->organization_id === Filament::getTenant()?->id),
            EditAction::make()
                ->visible(fn (Campaign $record): bool => $record->organization_id === Filament::getTenant()?->id),
        ];
    }
}
