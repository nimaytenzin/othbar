<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StoreOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return Auth::user()?->can('products.view') ?? false;
    }

    protected ?string $heading = 'Store catalog';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $visibleProducts = Product::query()->where('is_visible', true)->count();
        $totalProducts = Product::query()->count();
        $lowStock = Product::query()->where('stock_quantity', '<=', 5)->count();

        return [
            Stat::make('Visible products', (string) $visibleProducts)
                ->description('Listed on the storefront')
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('success')
                ->icon(Heroicon::OutlinedShoppingCart)
                ->url(ProductResource::getUrl('index')),

            Stat::make('Total products', (string) $totalProducts)
                ->description('Including hidden items')
                ->color('primary')
                ->icon(Heroicon::OutlinedSquares2x2)
                ->url(ProductResource::getUrl('index')),

            Stat::make('Low stock', (string) $lowStock)
                ->description('Five units or fewer')
                ->color($lowStock > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->url(ProductResource::getUrl('index')),
        ];
    }
}
