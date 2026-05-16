<?php

namespace App\Providers;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use App\Livewire\Admin\PaymentVerification;
use App\Models\Order;
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
    }
}
