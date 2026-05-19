<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Pages\CreateCounterOrder;
use App\Filament\Pages\OrderReport;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()?->can('orders.create')) {
            $actions[] = Action::make('createCounterOrder')
                ->label('Create order')
                ->icon('heroicon-o-plus')
                ->url(CreateCounterOrder::getUrl());
        }

        if (Auth::user()?->can('reports.view')) {
            $actions[] = Action::make('orderReport')
                ->label('Order report')
                ->icon('heroicon-o-document-chart-bar')
                ->url(OrderReport::getUrl());
        }

        return $actions;
    }
}
