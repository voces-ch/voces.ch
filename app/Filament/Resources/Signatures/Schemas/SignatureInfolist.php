<?php

namespace App\Filament\Resources\Signatures\Schemas;

use App\Filament\Resources\Campaigns\CampaignResource;
use App\Models\Signature;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use League\Flysystem\Visibility;

class SignatureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Flex::make([
                                Section::make('Submitted Data')
                                    ->description('The exact PII and custom fields provided by the signee.')
                                    ->schema([
                                        KeyValueEntry::make('payload')
                                            ->hiddenLabel()
                                            ->keyLabel('Field')
                                            ->valueLabel('Submitted Value'),
                                    ]),
                            ])
                            ->columnSpan(['default' => 2, 'md' => 2]),

                        Tabs::make("Further Info")
                            ->tabs([
                                Tab::make('Campaign Info')
                                    ->schema([
                                        TextEntry::make('campaign.title')
                                            ->label('Campaign')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->url(fn ($record) => CampaignResource::getUrl('view', ['record' => $record->campaign_id])),

                                        TextEntry::make('source.name')
                                            ->label('Gathered By (Source)')
                                            ->default('Organic (Main Page)')
                                            ->badge(),
                                        IconEntry::make('is_duplicate_of')
                                            ->label('Duplicate')
                                            ->icon('heroicon-o-document-duplicate')
                                            ->color('danger')
                                            ->visible(fn ($record) => $record->is_duplicate_of)
                                            ->tooltip('This signature was originally gathered by a different source, but because data pooling is enabled on the campaign, it has been imported into the current source as well.'),

                                        TextEntry::make('origin')
                                            ->label('Origin')
                                            ->badge(),
                                    ]),

                                Tab::make('Verification & Timing')
                                    ->schema([
                                        IconEntry::make('is_verified')
                                            ->label('DOI Status')
                                            ->boolean(),

                                        TextEntry::make('signed_at')
                                            ->label('Timestamp')
                                            ->dateTime('d.m.Y H:i:s'),
                                    ]),
                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
