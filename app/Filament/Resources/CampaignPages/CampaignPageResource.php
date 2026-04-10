<?php

namespace App\Filament\Resources\CampaignPages;

use App\Filament\Resources\CampaignPages\Pages\CreateCampaignPage;
use App\Filament\Resources\CampaignPages\Pages\EditCampaignPage;
use App\Filament\Resources\CampaignPages\Pages\ListCampaignPages;
use App\Filament\Resources\CampaignPages\Schemas\CampaignPageForm;
use App\Filament\Resources\CampaignPages\Tables\CampaignPagesTable;
use App\Models\CampaignPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CampaignPageResource extends Resource
{
    protected static ?string $model = CampaignPage::class;

    protected static string | UnitEnum | null $navigationGroup = 'Campaigning';
    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function getModelLabel(): string
    {
        return __('Campaign Page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Campaign Pages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Campaign Pages');
    }

    public static function getNavigationGroup(): ?string
    {
        // This groups resources together under a single collapsible header in the sidebar
        return __('Campaigning');
    }

    public static function form(Schema $schema): Schema
    {
        return CampaignPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignPagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignPages::route('/'),
            'create' => CreateCampaignPage::route('/create'),
            'edit' => EditCampaignPage::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
