<?php
namespace App\Filament\Pages\Tenancy;

use App\Filament\Schemas\Shared\CampaignFieldSchema;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EditOrganizationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('Organization Profile');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Basic Information'))
                    ->description(__('Update your organization\'s basic information. The UUID is used for integration with other organizations and cannot be changed.'))
                    ->schema([
                    TextInput::make('uuid')
                        ->disabled()
                        ->copyable()
                        ->helperText(__('The UUID is used to identify your organization when other organizations want to add you as a campaign partner. It cannot be changed.'))
                        ->label(__('UUID')),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, ?string $old, Set $set) {
                            if ($state === null || $state === $old) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->label(__('URL Slug'))
                        ->helperText(__('The URL slug is used in the URL to access your organization\'s profile. It must be unique across all organizations and can only contain letters, numbers, and hyphens.'))
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Select::make('default_locale')
                        ->label(__('Default Language'))
                        ->options([
                            'de' => 'Deutsch',
                            'fr' => 'Français',
                            'it' => 'Italiano',
                            'en' => 'English',
                        ])
                        ->required(),
                    ])
                    ->columns(2),
                Section::make(__('Default Campaign Fields'))
                    ->description(__('Define the default fields that will be included in every new campaign. These can be overridden at the campaign level.'))
                    ->schema([
                    Repeater::make('default_campaign_fields')
                        ->label(__('Default Campaign Fields'))
                        ->schema(CampaignFieldSchema::getFields())
                        ->rule(function () {
                            return function (string $attribute, mixed $value, Closure $fail) {
                                $uniqueCount = collect($value)->where('is_unique', true)->count();
                                if ($uniqueCount > 1) {
                                    $fail(__('Only one field can be marked as the unique identifier.'));
                                }
                                if ($uniqueCount === 0) {
                                    $fail(__('You must select exactly one field to be the unique identifier.'));
                                }

                                $uniqueField = collect($value)->firstWhere('is_unique', true);
                                if ($uniqueField && !in_array($uniqueField['type'], ['text', 'email'])) {
                                    $fail(__('The unique identifier field must be of type "text" or "email".'));
                                }

                                if ($uniqueField && !$uniqueField['is_required']) {
                                    $fail(__('The unique identifier field must be marked as required.'));
                                }

                                $fieldNames = collect($value)->pluck('name');
                                if ($fieldNames->count() !== $fieldNames->unique()->count()) {
                                    $fail(__('Each field must have a unique internal key.'));
                                }
                            };
                        })
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
