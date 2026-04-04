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

class SignatureInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // We define a 3-column grid for desktop displays
                Grid::make(3)
                    ->schema([
                        // MAIN CONTENT: Takes up 2 out of 3 columns
                        Flex::make([
                                Section::make('Submitted Data')
                                    ->description('The exact PII and custom fields provided by the signee.')
                                    ->schema([
                                        TextEntry::make('email')
                                            ->label('Signer Email')
                                            ->helperText('Used for double opt-in verification and to prevent duplicate signatures.')
                                            ->badge()
                                            ->color('primary'),
                                        KeyValueEntry::make('payload')
                                            ->hiddenLabel() // Keeps it clean
                                            ->keyLabel('Field')
                                            ->valueLabel('Submitted Value'),
                                    ]),
                            ])
                            // Full width on mobile, 2 columns on medium screens and up
                            ->columnSpan(['default' => 2, 'md' => 2]),

                        // META SIDEBAR: Takes up 1 out of 3 columns
                        Tabs::make("Further Info")
                            ->tabs([
                                Tab::make('Campaign Info')
                                    ->schema([
                                        TextEntry::make('campaign.title')
                                            ->label('Campaign')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->url(fn ($record) => CampaignResource::getUrl('view', ['record' => $record->campaign_id])),

                                        TextEntry::make('organization.name')
                                            ->label('Gathered By (Source)')
                                            ->default('Organic (Main Page)')
                                            ->badge()
                                            ->color(fn ($state) => $state === 'Organic (Main Page)' ? 'gray' : 'info'),
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
                    ->columnSpanFull(), // The grid itself takes full width, but its children define their own spans
            ]);
    }
}
