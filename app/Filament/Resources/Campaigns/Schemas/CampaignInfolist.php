<?php

namespace App\Filament\Resources\Campaigns\Schemas;

use App\Models\Campaign;
use App\Models\Organization;
use App\Models\Signature;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CampaignInfolist
{

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Details')
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make("description")
                                    ->html()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(['default' => 2, 'md' => 2]),
                        Tabs::make("Meta")
                            ->tabs([
                                Tab::make('Campaign Partners')
                                ->schema([
                                    RepeatableEntry::make('campaignPartners')
                                        ->label('Active Partners')
                                        ->schema([
                                            TextEntry::make('organization.name')
                                                ->label('Name')
                                                ->weight(FontWeight::Bold),
                                            TextEntry::make('source_slug')
                                                ->label('Slug')
                                                ->copyable()
                                                ->formatStateUsing(fn ($state) => "?source={$state}")
                                                ->color('info'),
                                            Actions::make([
                                                Action::make('remove_partner')
                                                    ->label("")
                                                    ->icon('heroicon-o-trash')
                                                    ->color('danger')
                                                    ->requiresConfirmation()
                                                    ->modalHeading('Remove Coalition Partner')
                                                    ->modalDescription('Are you sure? Their tracking link will stop working, but they will keep any data they have already exported.')
                                                    ->visible(function ($record) {
                                                        $tenantId = Filament::getTenant()?->id;

                                                        $hasSignatures = Signature::withoutGlobalScopes()
                                                            ->where('campaign_id', $record->campaign_id)
                                                            ->where('organization_id', $record->organization_id)
                                                            ->exists();

                                                        if ($hasSignatures) {
                                                            return false;
                                                        }

                                                        $isHost = $record->campaign->organization_id === $tenantId;
                                                        $isSelf = $record->organization_id === $tenantId;
                                                        return $isHost || $isSelf;
                                                    })
                                                    ->action(function ($record, \Livewire\Component $livewire) {
                                                        // Delete the CampaignPartner pivot record
                                                        $record->delete();

                                                        // The main Campaign record is attached to the Livewire component
                                                        $livewire->record->unsetRelation('campaignPartners');
                                                        $livewire->dispatch('$refresh');

                                                        Notification::make()
                                                            ->title('Partner removed')
                                                            ->success()
                                                            ->send();
                                                    }),
                                            ])->alignEnd()
                                        ])
                                        ->columns(3)
                                        ->placeholder('No coalition partners added yet.'),

                                    // The standalone action button at the bottom of the tab
                                    Actions::make([
                                        Action::make('add_partner')
                                            ->label('Attach Partner via UUID')
                                            ->icon('heroicon-o-link')
                                            ->color('primary')
                                            ->form([
                                                TextInput::make('organization_uuid')
                                                    ->label('Partner Organization UUID')
                                                    ->required()
                                                    ->uuid()
                                                    ->helperText('Ask the partner organization to provide their unique Workspace UUID.'),

                                                TextInput::make('source_slug')
                                                    ->label('Tracking Slug')
                                                    ->required()
                                                    ->helperText('e.g., sp-zh or campax. Must be unique for this campaign.')
                                            ])
                                            ->action(function (array $data, Campaign $record, Action $action, \Livewire\Component $livewire) {
                                                $partnerOrg = Organization::where('uuid', $data['organization_uuid'])->first();

                                                if (! $partnerOrg) {
                                                    Notification::make()
                                                        ->title('Organization not found.')
                                                        ->body('Please check the UUID and try again.')
                                                        ->danger()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($partnerOrg->id === $record->organization_id) {
                                                    Notification::make()
                                                        ->title('Invalid Partner')
                                                        ->body('You cannot add the campaign host as a partner.')
                                                        ->warning()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($record->campaignPartners()->where('organization_id', $partnerOrg->id)->exists()) {
                                                    Notification::make()
                                                        ->title('Already Attached')
                                                        ->body('This organization is already a partner on this campaign.')
                                                        ->warning()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($record->campaignPartners()->where('source_slug', $data['source_slug'])->exists()) {
                                                    Notification::make()
                                                        ->title('Slug already in use')
                                                        ->body("The tracking slug '{$data['source_slug']}' is already assigned to another partner on this campaign. Please choose a unique one.")
                                                        ->danger()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                $record->campaignPartners()->create([
                                                    'organization_id' => $partnerOrg->id,
                                                    'source_slug' => $data['source_slug'],
                                                ]);

                                                $record->unsetRelation('campaignPartners');
                                                $livewire->dispatch('$refresh');

                                                Notification::make()
                                                    ->title('Partner successfully attached!')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->visible(fn (Campaign $record): bool => $record->organization_id === Filament::getTenant()?->id),
                                    ])->fullWidth(),
                                ]),

                                Tab::make('Custom Fields')
                                    ->schema([
                                        RepeatableEntry::make('campaignFields')
                                            ->label('Custom Fields')
                                            ->schema([
                                                TextEntry::make('label')
                                                    ->label('Public Label'),
                                                IconEntry::make('is_required')
                                                    ->boolean()
                                                    ->label('Required?'),
                                            ])
                                            ->columns(2)
                                            ->placeholder('-')
                                            ->columnSpanFull(),
                                    ]),

                                Tab::make('General Info')
                                    ->schema([
                                        TextEntry::make('slug'),
                                        TextEntry::make('uuid')
                                            ->label('UUID'),
                                        IconEntry::make('is_active')
                                            ->boolean(),
                                    ]),

                                Tab::make('Organization & Timing')
                                    ->schema([
                                        TextEntry::make('organization.name')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->url(fn ($record) => $record->organization ? route('filament.admin.tenant.profile', ['tenant' => $record->organization]) : null)
                                            ->label('Organization'),
                                        TextEntry::make('created_at')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->dateTime()
                                            ->placeholder('-'),
                                        TextEntry::make('deleted_at')
                                            ->dateTime()
                                            ->visible(fn (Campaign $record): bool => $record->trashed()),
                                    ]),
                            ])
                            // Full width on mobile, 1 column on medium screens and up
                            ->columnSpan(['default' => 3, 'md' => 1]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
