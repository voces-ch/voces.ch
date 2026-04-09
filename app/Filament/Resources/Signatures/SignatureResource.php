<?php

namespace App\Filament\Resources\Signatures;

use App\Filament\Resources\Signatures\Pages\CreateSignature;
use App\Filament\Resources\Signatures\Pages\EditSignature;
use App\Filament\Resources\Signatures\Pages\ListSignatures;
use App\Filament\Resources\Signatures\Pages\ViewSignature;
use App\Filament\Resources\Signatures\Schemas\SignatureForm;
use App\Filament\Resources\Signatures\Schemas\SignatureInfolist;
use App\Filament\Resources\Signatures\Tables\SignaturesTable;
use App\Models\Signature;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SignatureResource extends Resource
{
    protected static ?string $model = Signature::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;
    protected static string|UnitEnum|null $navigationGroup = 'Data';

    protected static bool $isScopedToTenant = false;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function form(Schema $schema): Schema
    {
        return SignatureForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SignatureInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SignaturesTable::configure($table);
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
            'index' => ListSignatures::route('/'),
            'create' => CreateSignature::route('/create'),
            'view' => ViewSignature::route('/{record}'),
            'edit' => EditSignature::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        $tenantId = Filament::getTenant()?->id;

        return parent::getGlobalSearchEloquentQuery()
            ->where(function ($query) use ($tenantId) {
                $query->where('organization_id', $tenantId)
                    ->orWhereHas('campaign', function ($campaignQuery) use ($tenantId) {
                        $campaignQuery->where('organization_id', $tenantId)
                                    ->where('is_data_pooled', true);
                    });
            });
    }
}
