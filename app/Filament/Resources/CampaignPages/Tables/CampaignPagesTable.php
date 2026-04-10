<?php

namespace App\Filament\Resources\CampaignPages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CampaignPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_published')
                    ->label(__('Published'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('campaign.title')
                    ->label(__('Campaign'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->sortable()
                    ->copyable()
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('No campaign pages found'))
            ->emptyStateDescription(__('Get started by creating a new campaign page.'));
    }
}
