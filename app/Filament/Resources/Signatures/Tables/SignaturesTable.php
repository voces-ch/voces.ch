<?php

namespace App\Filament\Resources\Signatures\Tables;

use App\Filament\Exports\SignatureExporter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SignaturesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label(__('UUID'))
                    ->searchable(),
                IconColumn::make('is_verified')
                    ->label(__('Verified'))
                    ->boolean(),
                TextColumn::make('signed_at')
                    ->label(__('Signed At'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('campaign.title')
                    ->label(__('Campaign'))
                    ->searchable(),
                TextColumn::make('source.name')
                    ->label(__('Organization'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('Deleted At'))
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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('No signatures found'))
            ->emptyStateDescription(__('Start a campaign and collect signatures using the signature collection form.'));
    }
}
