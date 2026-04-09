<?php

namespace App\Filament\Resources\Signatures\Tables;

use App\Filament\Exports\SignatureExporter;
use App\Models\Organization;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SignaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                IconColumn::make('is_verified')
                    ->boolean(),
                TextColumn::make('signed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('campaign.title')
                    ->searchable(),
                TextColumn::make('organization.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->HeaderActions([
                ExportAction::make()
                    ->exporter(SignatureExporter::class)
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $tenantId = Filament::getTenant()?->id;
                $query->where(function ($query) use ($tenantId) {

                        $query->where('organization_id', $tenantId)

                            ->orWhereHas('campaign', function ($campaignQuery) use ($tenantId) {
                                $campaignQuery->where('organization_id', $tenantId)
                                                ->where('is_data_pooled', true);
                            });
                    });
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
