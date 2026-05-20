<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InvoiceStatsOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Accounts receivable';

    protected ?string $description = 'Open balances and collection snapshot';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->can('invoices.view') ?? false;
    }

    protected function getStats(): array
    {
        $outstandingMinor = Invoice::outstandingBalanceMinor();
        $outstandingCount = Invoice::query()->outstanding()->count();
        $overdueMinor = Invoice::overdueBalanceMinor();
        $overdueCount = Invoice::query()->overdue()->count();
        $partialCount = Invoice::query()->partiallyPaid()->count();
        $paidCount = Invoice::query()->paid()->count();

        $indexUrl = fn (string $tab): string => InvoiceResource::getUrl('index', ['tab' => $tab]);

        return [
            Stat::make('Outstanding', Invoice::formatMinor($outstandingMinor))
                ->description($outstandingCount.' open invoice'.($outstandingCount === 1 ? '' : 's'))
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color($outstandingCount > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->url($indexUrl('outstanding')),

            Stat::make('Overdue', Invoice::formatMinor($overdueMinor))
                ->description($overdueCount.' past due')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color($overdueCount > 0 ? 'danger' : 'gray')
                ->icon(Heroicon::OutlinedClock)
                ->url($indexUrl('overdue')),

            Stat::make('Partially paid', (string) $partialCount)
                ->description('Payments recorded, balance remains')
                ->descriptionIcon(Heroicon::OutlinedReceiptPercent)
                ->color($partialCount > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedScale)
                ->url($indexUrl('partial')),

            Stat::make('Paid in full', (string) $paidCount)
                ->description('Settled invoices')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->icon(Heroicon::OutlinedCheckBadge)
                ->url($indexUrl('paid')),
        ];
    }
}
