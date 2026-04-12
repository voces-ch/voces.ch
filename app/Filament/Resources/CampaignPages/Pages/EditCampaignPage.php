<?php

namespace App\Filament\Resources\CampaignPages\Pages;

use App\Filament\Resources\CampaignPages\CampaignPageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCampaignPage extends EditRecord
{
    protected static string $resource = CampaignPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
