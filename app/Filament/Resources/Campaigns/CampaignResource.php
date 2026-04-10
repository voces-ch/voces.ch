<?php

namespace App\Filament\Resources\Campaigns;

use App\Filament\Resources\Campaigns\Pages\CreateCampaign;
use App\Filament\Resources\Campaigns\Pages\EditCampaign;
use App\Filament\Resources\Campaigns\Pages\ListCampaigns;
use App\Filament\Resources\Campaigns\Pages\ViewCampaign;
use App\Filament\Resources\Campaigns\RelationManagers\IntegrationsRelationManager;
use App\Filament\Resources\Campaigns\RelationManagers\SignaturesRelationManager;
use App\Filament\Resources\Campaigns\Schemas\CampaignForm;
use App\Filament\Resources\Campaigns\Schemas\CampaignInfolist;
use App\Filament\Resources\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use App\Models\Integration;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string | UnitEnum | null $navigationGroup = 'Campaigning';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;


    protected static ?string $recordTitleAttribute = 'title';

    public static function getModelLabel(): string
    {
        return __('Campaign');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Campaigns');
    }

    public static function getNavigationLabel(): string
    {
        return __('Campaigns');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Campaigning');
    }

    public static function form(Schema $schema): Schema
    {
        $campaign = $schema->getRecord();
        return CampaignForm::configure($schema, $campaign);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SignaturesRelationManager::class,
            IntegrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $tenantId = Filament::getTenant()?->id;

        return parent::getEloquentQuery()->where(function (Builder $query) use ($tenantId) {
            $query->where('organization_id', $tenantId)
                  ->orWhereHas('campaignPartners', function (Builder $q) use ($tenantId) {
                      $q->where('organization_id', $tenantId);
                  });
        });
    }

    public static function canEdit(Model $record): bool
    {
        return $record->organization_id === Filament::getTenant()?->id;
    }

    public static function canDelete(Model $record): bool
    {
        return $record->organization_id === Filament::getTenant()?->id;
    }
}
