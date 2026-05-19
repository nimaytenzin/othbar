<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use App\Livewire\Admin\PaymentVerification;
use App\Models\Order;
use App\Models\SiteSetting;
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

        Order::updating(function (Order $order): void {
            if ($order->isDirty('status') && $order->status === OrderStatus::Completed) {
                $order->shipping_status = ShippingStatus::Delivered;
            }
        });

        \Illuminate\Support\Facades\Gate::policy(\App\Models\Order::class, \App\Policies\OrderPolicy::class);

        \Illuminate\Support\Facades\Gate::before(function ($user, string $ability) {
            if ($user instanceof \App\Models\User && $user->hasRole('administrator')) {
                return true;
            }

            return null;
        });

        View::composer(['storefront.layout', 'storefront.*'], function ($view): void {
            $view->with('site', SiteSetting::current());
        });
    }
}
