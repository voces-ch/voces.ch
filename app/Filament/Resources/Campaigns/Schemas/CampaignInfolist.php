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
use Filament\Schemas\Components\Utilities\Get;
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
                        Tabs::make('Details')
                            ->tabs([
                                Tab::make(__('General'))
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label(__('Title')),
                                        TextEntry::make('description')
                                            ->label(__('Description'))
                                            ->html(),
                                        IconEntry::make('is_active')
                                            ->boolean()
                                            ->label(__('Active')),
                                        IconEntry::make('is_data_pooled')
                                            ->boolean()
                                            ->label(__('Data Pooled?')),
                                    ])
                                    ->columns(2),
                                Tab::make(__('Campaign Goal'))
                                    ->schema([
                                        IconEntry::make('has_goal')
                                            ->boolean()
                                            ->label(__('Has Goal?')),
                                        TextEntry::make('goal')
                                            ->label(__('Goal Target')),
                                        TextEntry::make('goal_type')
                                            ->label(__('Goal Type')),
                                        TextEntry::make('goal_field')
                                            ->label(__('Goal Field')),
                                    ])
                                    ->columns(2),
                                Tab::make(__('Success Logic'))
                                    ->schema([
                                        TextEntry::make('success_type')
                                            ->label(__('Success Type'))
                                            ->badge()
                                            ->color(fn ($state) => match ($state) {
                                                'message' => 'success',
                                                'redirect' => 'primary',
                                                default => 'secondary',
                                            })
                                            ->formatStateUsing(fn ($state) => ucfirst($state)),
                                        TextEntry::make('success_message')
                                            ->label(__('Success Message'))
                                            ->html()
                                            ->visible(fn (Get $get) => $get('success_type') === 'message'),
                                        TextEntry::make('success_url')
                                            ->url(fn($state) => $state, true)
                                            ->formatStateUsing(function($state) {
                                                if (! $state) {
                                                    return null;
                                                }
                                                $displayUrl = preg_replace('#^https?://#', '', rtrim($state, '/'));
                                                $displayUrl = preg_replace('#^www\.#', '', $displayUrl);
                                                return $displayUrl;
                                            })
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->label(__('Success URL'))
                                            ->visible(fn (Get $get) => $get('success_type') === 'redirect'),
                                    ]),
                                Tab::make(__('Email verification'))
                                    ->schema([
                                        IconEntry::make('is_email_verification_enabled')
                                            ->boolean()
                                            ->label(__('Requires Email Verification?')),
                                        TextEntry::make('email_verification_field')
                                            ->label(__('Verification Email Field Name'))
                                            ->badge()
                                            ->helperText(__('The name of the email field that requires verification.')),
                                    ])
                                    ->columns(2)
                            ])
                            ->columnSpan(['default' => 2, 'md' => 2]),
                        Tabs::make("Meta")
                            ->tabs([
                                Tab::make(__('Campaign Partners'))
                                ->schema([
                                    RepeatableEntry::make('campaignPartners')
                                        ->label(__('Active Partners'))
                                        ->schema([
                                            TextEntry::make('organization.name')
                                                ->label(__('Name'))
                                                ->weight(FontWeight::Bold),
                                            TextEntry::make('source_slug')
                                                ->label(__('Slug'))
                                                ->copyable()
                                                ->formatStateUsing(fn ($state) => "?source={$state}")
                                                ->color('info'),
                                            Actions::make([
                                                Action::make('remove_partner')
                                                    ->label(__('Remove Partner'))
                                                    ->icon('heroicon-o-trash')
                                                    ->color('danger')
                                                    ->requiresConfirmation()
                                                    ->modalHeading(__('Remove Coalition Partner'))
                                                    ->modalDescription(__('Are you sure? Their tracking link will stop working, but they will keep any data they have already exported.'))
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
                                                        $record->delete();

                                                        $livewire->record->unsetRelation('campaignPartners');
                                                        $livewire->dispatch('$refresh');

                                                        Notification::make()
                                                            ->title(__('Partner removed'))
                                                            ->success()
                                                            ->send();
                                                    }),
                                            ])
                                            ->alignEnd()
                                            ->columnSpanFull()
                                            ->fullWidth()
                                        ])
                                        ->columns(1)
                                        ->placeholder(__('No campaign partners added yet.')),

                                    Actions::make([
                                        Action::make('add_partner')
                                            ->label(__('Attach Partner via UUID'))
                                            ->icon('heroicon-o-link')
                                            ->color('primary')
                                            ->form([
                                                TextInput::make('organization_uuid')
                                                    ->label(__('Partner Organization UUID'))
                                                    ->required()
                                                    ->uuid()
                                                    ->helperText(__('Ask the partner organization to provide their unique Workspace UUID.')),

                                                TextInput::make('source_slug')
                                                    ->label(__('Tracking Slug'))
                                                    ->required()
                                                    ->helperText(__('e.g., sp-zh or campax. Must be unique for this campaign.'))
                                            ])
                                            ->action(function (array $data, Campaign $record, Action $action, \Livewire\Component $livewire) {
                                                $partnerOrg = Organization::where('uuid', $data['organization_uuid'])->first();

                                                if (! $partnerOrg) {
                                                    Notification::make()
                                                        ->title(__('Organization not found.'))
                                                        ->body(__('Please check the UUID and try again.'))
                                                        ->danger()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($partnerOrg->id === $record->organization_id) {
                                                    Notification::make()
                                                        ->title(__('Invalid Partner'))
                                                        ->body(__('You cannot add the campaign host as a partner.'))
                                                        ->warning()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($record->campaignPartners()->where('organization_id', $partnerOrg->id)->exists()) {
                                                    Notification::make()
                                                        ->title(__('Already Attached'))
                                                        ->body(__('This organization is already a partner on this campaign.'))
                                                        ->warning()
                                                        ->send();

                                                    $action->halt();
                                                }

                                                if ($record->campaignPartners()->where('source_slug', $data['source_slug'])->exists()) {
                                                    Notification::make()
                                                        ->title(__('Slug already in use'))
                                                        ->body(__('The tracking slug {:slug} is already assigned to another partner on this campaign. Please choose a unique one.', ['slug' => $data['source_slug']]))
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
                                                    ->title(__('Partner successfully attached!'))
                                                    ->success()
                                                    ->send();
                                            })
                                            ->visible(fn (Campaign $record): bool => $record->organization_id === Filament::getTenant()?->id),
                                    ])->fullWidth(),
                                ]),

                                Tab::make(__('Organization & Timing'))
                                    ->schema([
                                        TextEntry::make('organization.name')
                                            ->weight(FontWeight::Bold)
                                            ->color('primary')
                                            ->url(fn ($record) => $record->organization ? route('filament.admin.tenant.profile', ['tenant' => $record->organization]) : null)
                                            ->label(__('Organization')),
                                        TextEntry::make('created_at')
                                            ->dateTime()
                                            ->label(__('Created At'))
                                            ->placeholder('-'),
                                        TextEntry::make('updated_at')
                                            ->dateTime()
                                            ->label(__('Updated At'))
                                            ->placeholder('-'),
                                        TextEntry::make('deleted_at')
                                            ->dateTime()
                                            ->label(__('Deleted At'))
                                            ->visible(fn (Campaign $record): bool => $record->trashed()),
                                    ]),
                            ])
                            ->columnSpan(['default' => 3, 'md' => 1]),
                    ])
                    ->columnSpanFull(),
                    Section::make(__("Campaign Fields"))
                        ->schema([
                            RepeatableEntry::make('campaignFields')
                                ->label(__('Campaign Fields'))
                                ->schema([
                                    TextEntry::make('label')
                                        ->html()
                                        ->label(__('Public Label')),
                                    TextEntry::make('name')
                                        ->label(__('Internal Name'))
                                        ->copyable()
                                        ->helperText(__('Used for data exports and integrations. Must be unique.')),
                                    TextEntry::make('type')
                                        ->label(__('Field Type'))
                                        ->badge()
                                        ->color(fn ($state) => match ($state) {
                                            'text' => 'primary',
                                            'email' => 'success',
                                            'number' => 'warning',
                                            default => 'secondary',
                                        })
                                        ->formatStateUsing(fn ($state) => ucfirst($state)),
                                    TextEntry::make('default_value')
                                        ->label(__('Default Value'))
                                        ->helperText(__('Optional default value for this field.')),
                                    IconEntry::make('is_unique')
                                        ->boolean()
                                        ->label(__('Must be Unique?')),
                                    IconEntry::make('is_required')
                                        ->boolean()
                                        ->label(__('Required?')),
                                ])
                                ->columns(2)
                                ->placeholder('-')
                                ->columnSpanFull(),
                        ])
                        ->description(__('Custom fields associated with this campaign. These fields will appear on the signup form and be included in exports.'))
                        ->columnSpanFull()
                        ->collapsed(true)
                        ->collapsible(),
            ]);
    }
}
