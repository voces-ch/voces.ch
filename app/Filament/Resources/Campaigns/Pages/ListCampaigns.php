<?php

namespace App\Filament\Resources\Campaigns\Pages;

use App\Filament\Resources\Campaigns\CampaignResource;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tenantId = Filament::getTenant()?->id;

        return [
            'all' => Tab::make('All Campaigns')
                ->label(__('All Campaigns')),

            'hosted' => Tab::make('Hosted by Us')
                ->label(__('Hosted by Us'))
                ->icon('heroicon-o-home')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('organization_id', $tenantId)),

            'partner' => Tab::make('Coalition Partner')
                ->label(__('Coalition Partner'))
                ->icon('heroicon-o-link')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('organization_id', '!=', $tenantId)),
        ];
    }
}
