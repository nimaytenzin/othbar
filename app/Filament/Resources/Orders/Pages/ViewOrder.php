<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedSchema::make('infolist'),
                SchemaView::make('filament.orders.extras')
                    ->viewData(fn (): array => ['order' => $this->getRecord()]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('receipt')
                ->label('Print receipt')
                ->url(fn (Order $record): string => route('admin.orders.receipt', $record).'?autoprint=1')
                ->openUrlInNewTab(),
            Action::make('markProcessing')
                ->label('Mark processing')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::New && $record->payment_status === PaymentStatus::Paid)
                ->action(fn (Order $record) => $record->update([
                    'status' => OrderStatus::Processing,
                    'shipping_status' => ShippingStatus::Pending,
                ])),
            Action::make('markShipped')
                ->label('Mark shipped')
                ->visible(fn (Order $record): bool => $record->status === OrderStatus::Processing && $record->payment_status === PaymentStatus::Paid)
                ->action(fn (Order $record) => $record->update([
                    'status' => OrderStatus::Shipped,
                    'shipping_status' => ShippingStatus::Shipped,
                ])),
            Action::make('markComplete')
                ->label('Mark complete')
                ->visible(fn (Order $record): bool => in_array($record->status, [OrderStatus::Processing, OrderStatus::Shipped], true)
                    && $record->payment_status === PaymentStatus::Paid)
                ->action(fn (Order $record) => $record->update([
                    'status' => OrderStatus::Completed,
                ])),
            Action::make('cancel')
                ->label('Cancel order')
                ->color('danger')
                ->visible(fn (Order $record): bool => ! in_array($record->status, [OrderStatus::Completed, OrderStatus::Cancelled], true))
                ->requiresConfirmation()
                ->action(function (Order $record): void {
                    $record->update([
                        'status' => OrderStatus::Cancelled,
                        'payment_status' => $record->payment_status === PaymentStatus::Pending
                            ? PaymentStatus::Voided
                            : $record->payment_status,
                    ]);
                }),
            EditAction::make(),
        ];
    }
}
