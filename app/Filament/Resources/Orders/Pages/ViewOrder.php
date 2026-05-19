<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    #[On('order-payment-approved')]
    #[On('order-payment-rejected')]
    public function refreshOrderView(): void
    {
        if (! $this->hasRecord()) {
            return;
        }

        $this->record->refresh();
        $this->record->load(['items', 'shippingAddress']);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.orders.view-order')
                    ->viewData(fn (): array => [
                        'order' => $this->getRecord()->load(['items', 'shippingAddress']),
                    ]),
            ]);
    }

    public function markFulfilled(): void
    {
        /** @var Order $order */
        $order = $this->getRecord();

        if (! $order->canMarkFulfilled()) {
            return;
        }

        $order->update([
            'status' => OrderStatus::Completed,
        ]);

        Notification::make()
            ->title(match (true) {
                $order->isCounter() => 'Counter sale marked as fulfilled',
                $order->isPickup() => 'Pickup marked as fulfilled',
                default => 'Delivery marked as fulfilled',
            })
            ->success()
            ->send();
    }

    public function cancelOrder(): void
    {
        /** @var Order $order */
        $order = $this->getRecord();

        if (in_array($order->status, [OrderStatus::Completed, OrderStatus::Cancelled], true)) {
            return;
        }

        $order->update([
            'status' => OrderStatus::Cancelled,
            'payment_status' => $order->payment_status === PaymentStatus::Pending
                ? PaymentStatus::Voided
                : $order->payment_status,
        ]);

        Notification::make()
            ->title('Order cancelled')
            ->danger()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('receipt')
                ->label('Print receipt')
                ->icon('heroicon-o-printer')
                ->url(fn (Order $record): string => route('filament.admin.orders.receipt', $record).'?autoprint=1')
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
