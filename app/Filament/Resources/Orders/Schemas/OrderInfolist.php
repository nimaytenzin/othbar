<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('number')
                    ->label('Order #'),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->color(fn (OrderStatus $state): string => $state->getColor())
                    ->icon(fn (OrderStatus $state): ?string => $state->getIcon()),
                TextEntry::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->getLabel())
                    ->color(fn (PaymentStatus $state): string => $state->getColor())
                    ->icon(fn (PaymentStatus $state): ?string => $state->getIcon()),
                TextEntry::make('shipping_status')
                    ->badge()
                    ->formatStateUsing(fn (ShippingStatus $state): string => $state->getLabel())
                    ->color(fn (ShippingStatus $state): string => $state->getColor())
                    ->icon(fn (ShippingStatus $state): ?string => $state->getIcon()),
                TextEntry::make('fulfillment_method')->badge(),
                TextEntry::make('total_minor')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => 'Nu. '.number_format(((int) $state) / 100)),
                TextEntry::make('currency_code'),
                TextEntry::make('payment_reference'),
                TextEntry::make('payment_access_token')
                    ->label('Pay token')
                    ->copyable()
                    ->placeholder('—'),
                TextEntry::make('shippingAddress.full_name')->label('Customer name'),
                TextEntry::make('shippingAddress.phone')->label('Phone'),
                TextEntry::make('shippingAddress.street_address'),
                TextEntry::make('shippingAddress.city'),
                TextEntry::make('shippingAddress.postal_code'),
                TextEntry::make('notes')->columnSpanFull(),
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('line_items')
                    ->label('Line items')
                    ->state(fn (Order $record): string => $record->items
                        ->map(fn ($i) => $i->name.' × '.$i->quantity.' — Nu. '.number_format($i->line_total_minor / 100))
                        ->implode("\n"))
                    ->columnSpanFull(),
            ]);
    }
}
