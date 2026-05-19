<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\Products\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StoreOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $visibleProducts = Product::query()->where('is_visible', true)->count();
        $newOrders = Order::query()->where('status', OrderStatus::New)->count();

        return [
            Stat::make('Visible products', (string) $visibleProducts)
                ->description('Listed on the storefront')
                ->color('success'),
            Stat::make('New orders', (string) $newOrders)
                ->description('Awaiting payment or fulfillment')
                ->color($newOrders > 0 ? 'warning' : 'gray'),
            Stat::make('Catalog', 'Products')
                ->description('Manage products & photos')
                ->url(ProductResource::getUrl('index')),
        ];
    }
}
