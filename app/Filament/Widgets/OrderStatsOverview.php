<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Orders\OrderResource;
use App\Support\OrderDashboardStats;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OrderStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()?->can('orders.view') ?? false;
    }

    protected ?string $heading = 'Orders overview';

    protected ?string $description = 'Sales and fulfillment snapshot for your store';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $stats = app(OrderDashboardStats::class);

        return [
            Stat::make('This month sales', OrderDashboardStats::formatMoney($stats->monthSalesRevenueMinor()))
                ->description($stats->monthSalesTrendDescription())
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->chart($stats->lastSevenDayRevenueSparkline())
                ->chartColor('#2D5440')
                ->url(OrderResource::getUrl('index')),

            Stat::make('Orders today', (string) $stats->todayOrdersCount())
                ->description('Placed since midnight')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color($stats->todayOrdersCount() > 0 ? 'primary' : 'gray')
                ->icon(Heroicon::OutlinedShoppingBag)
                ->url(OrderResource::getUrl('index')),

            Stat::make('Pending payment', (string) $stats->pendingPaymentCount())
                ->description($stats->pendingPaymentDescription())
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($stats->pendingPaymentCount() > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedCreditCard)
                ->url(OrderResource::getUrl('index')),

            Stat::make('Awaiting fulfillment', (string) $stats->awaitingFulfillmentCount())
                ->description('Paid but not yet delivered or picked up')
                ->descriptionIcon(Heroicon::OutlinedTruck)
                ->color($stats->awaitingFulfillmentCount() > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedArchiveBox)
                ->url(OrderResource::getUrl('index')),

            Stat::make('Fulfilled this month', (string) $stats->fulfilledThisMonthCount())
                ->description('Completed deliveries and pickups')
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color('success')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->url(OrderResource::getUrl('index')),

            Stat::make('Total active orders', (string) $stats->totalActiveOrdersCount())
                ->description($stats->paidOrdersCount().' paid · excludes cancelled')
                ->descriptionIcon(Heroicon::OutlinedQueueList)
                ->color('primary')
                ->icon(Heroicon::OutlinedClipboardDocumentList)
                ->url(OrderResource::getUrl('index')),
        ];
    }
}
