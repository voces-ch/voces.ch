<?php

namespace App\Filament\Exports;

use App\Models\CampaignField;
use App\Models\Signature;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class SignatureExporter extends Exporter
{
    protected static ?string $model = Signature::class;

    public static function getColumns(): array
    {
        $columns = [
            ExportColumn::make('campaign.title')
                ->label(__('Campaign')),

            ExportColumn::make('source.name')
                ->label(__('Gathered By (Source)')),
            ExportColumn::make('isDuplicate')
                ->label(__('Is Duplicate'))
                ->state(fn (Signature $record) => $record->isDuplicate() ? 'Yes' : 'No'),

            ExportColumn::make('signed_at')
                ->label(__('Date Signed')),

            ExportColumn::make('is_verified')
                ->label(__('DOI Verified'))
                ->state(fn (Signature $record) => $record->is_verified ? 'Yes' : 'No'),

            ExportColumn::make('origin')
                ->label(__('Origin')),
        ];

        $tenantId = Filament::getTenant()?->id;

        if (app()->runningInConsole() && ! $tenantId) {
            $uniqueKeys = CampaignField::withoutGlobalScopes()
                ->distinct()
                ->pluck('name');
        } else {
            $uniqueKeys = CampaignField::whereHas('campaign', function (Builder $query) use ($tenantId) {
                $query->where('organization_id', $tenantId)
                      ->orWhereHas('campaignPartners', function (Builder $partnerQuery) use ($tenantId) {
                          $partnerQuery->where('organization_id', $tenantId);
                      });
            })
            ->distinct()
            ->pluck('name');
        }

        foreach ($uniqueKeys as $key) {
            $columns[] = ExportColumn::make("custom_field_{$key}")
                ->label(Str::headline($key))
                ->state(function (Signature $record) use ($key) {
                    $payload = $record->payload ?? [];
                    return (string) ($payload[$key] ?? '');
                });
        }

        return $columns;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your signature export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
