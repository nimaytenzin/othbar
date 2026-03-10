<?php

namespace App\Providers;

use App\Livewire\Admin\PaymentVerification;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Shopper\Core\Enum\OrderStatus;
use Shopper\Core\Enum\ShippingStatus;
use Shopper\Core\Models\Order;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('admin.payment-verification', PaymentVerification::class);

        // When an order is marked Complete, automatically mark shipping as Delivered.
        Order::updating(function (Order $order): void {
            if ($order->isDirty('status') && $order->status === OrderStatus::Completed) {
                $order->shipping_status = ShippingStatus::Delivered;
            }
        });
    }
}
