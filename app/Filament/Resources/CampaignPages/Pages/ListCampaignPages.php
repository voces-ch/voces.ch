<?php

namespace App\Filament\Resources\CampaignPages\Pages;

use App\Filament\Resources\CampaignPages\CampaignPageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampaignPages extends ListRecords
{
    protected static string $resource = CampaignPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
