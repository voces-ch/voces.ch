<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Filament\Exports\SignatureExporter;
use App\Filament\Resources\Signatures\SignatureResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class SignaturesRelationManager extends RelationManager
{
    protected static string $relationship = 'signatures';

    protected static ?string $relatedResource = SignatureResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                ExportAction::make()
                    ->exporter(SignatureExporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),
            ]);
    }
}
