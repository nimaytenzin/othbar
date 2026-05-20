<?php

namespace App\Filament\Widgets\Inventory;

use App\Filament\Resources\Products\ProductResource;
use App\Models\InventoryMovement;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class InventoryStatsWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tracked = Product::query()->where('track_inventory', true);
        $trackedCount = (clone $tracked)->count();

        $lowStock = (clone $tracked)
            ->where(function ($query): void {
                $query->where(function ($q): void {
                    $q->whereNotNull('reorder_level')
                        ->whereColumn('stock_quantity', '<=', 'reorder_level');
                })->orWhere(function ($q): void {
                    $q->whereNull('reorder_level')
                        ->where('stock_quantity', '<=', 5);
                });
            })
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStock = (clone $tracked)->where('stock_quantity', '<=', 0)->count();

        $movementsToday = InventoryMovement::query()
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Tracked products', (string) $trackedCount)
                ->description('Inventory tracking enabled')
                ->descriptionIcon(Heroicon::OutlinedArchiveBox)
                ->color('primary')
                ->icon(Heroicon::OutlinedCube)
                ->url(ProductResource::getUrl('index')),

            Stat::make('Low stock', (string) $lowStock)
                ->description('At or below reorder level')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color($lowStock > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedBellAlert),

            Stat::make('Out of stock', (string) $outOfStock)
                ->description('Zero units on hand')
                ->color($outOfStock > 0 ? 'danger' : 'gray')
                ->icon(Heroicon::OutlinedXCircle),

            Stat::make('Movements today', (string) $movementsToday)
                ->description('Sales, restocks & adjustments')
                ->color('success')
                ->icon(Heroicon::OutlinedArrowsRightLeft),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->can('inventory.view') ?? false;
    }
}
