<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use App\Livewire\Admin\CounterPaymentRecording;
use App\Livewire\Admin\PaymentVerification;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Services\StockService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Filament uploads must use a public disk so storefront URLs work via /storage.
        config(['filament.default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public')]);

        Livewire::component('admin.payment-verification', PaymentVerification::class);
        Livewire::component('admin.counter-payment-recording', CounterPaymentRecording::class);

        Order::updating(function (Order $order): void {
            if ($order->isDirty('status') && $order->status === OrderStatus::Completed) {
                $order->shipping_status = ShippingStatus::Delivered;
                app(StockService::class)->decrementForOrder($order);
            }

            if ($order->isDirty('status') && $order->status === OrderStatus::Cancelled) {
                $originalStatus = $order->getOriginal('status');

                if ($originalStatus instanceof OrderStatus) {
                    $wasCompleted = $originalStatus === OrderStatus::Completed;
                } else {
                    $wasCompleted = $originalStatus === OrderStatus::Completed->value;
                }

                if ($wasCompleted) {
                    app(StockService::class)->restockForOrder($order);
                }
            }
        });

        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        View::composer(['storefront.layout', 'storefront.*'], function ($view): void {
            $view->with('site', SiteSetting::current());
        });
    }
}
