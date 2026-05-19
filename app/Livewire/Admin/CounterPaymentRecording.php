<?php

namespace App\Livewire\Admin;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\CounterOrderService;
use App\Support\PaymentMethods;
use Livewire\Component;

class CounterPaymentRecording extends Component
{
    public Order $order;

    public string $payment_method = 'cash';

    public string $payment_bank = '';

    public string $payment_reference = '';

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function updatedPaymentMethod(): void
    {
        if ($this->payment_method !== PaymentMethods::MODE_BANK_TRANSFER) {
            $this->payment_bank = '';
        }
    }

    public function recordPaymentAndFulfill(CounterOrderService $counterOrderService): void
    {
        if (! $this->order->isCounter() || $this->order->payment_status !== PaymentStatus::Pending) {
            return;
        }

        $this->authorize('update', $this->order);

        PaymentMethods::validateCounterPayment(
            $this->payment_method,
            filled($this->payment_bank) ? $this->payment_bank : null,
            filled($this->payment_reference) ? $this->payment_reference : null,
        );

        $counterOrderService->recordPaymentAndFulfill(
            $this->order,
            $this->payment_method,
            filled($this->payment_reference) ? $this->payment_reference : null,
            filled($this->payment_bank) ? $this->payment_bank : null,
        );

        $this->order->refresh();
        $this->reset('payment_reference', 'payment_bank');

        session()->flash('payment_verified', 'Payment recorded and order marked as fulfilled.');
        $this->dispatch('order-payment-approved');
    }

    public function recordPayment(CounterOrderService $counterOrderService): void
    {
        if (! $this->order->isCounter() || $this->order->payment_status !== PaymentStatus::Pending) {
            return;
        }

        $this->authorize('update', $this->order);

        PaymentMethods::validateCounterPayment(
            $this->payment_method,
            filled($this->payment_bank) ? $this->payment_bank : null,
            filled($this->payment_reference) ? $this->payment_reference : null,
        );

        $counterOrderService->recordPayment(
            $this->order,
            $this->payment_method,
            filled($this->payment_reference) ? $this->payment_reference : null,
            filled($this->payment_bank) ? $this->payment_bank : null,
        );

        $this->order->refresh();
        $this->reset('payment_reference', 'payment_bank');

        session()->flash('payment_verified', 'Payment recorded. You can now mark the order as fulfilled.');
        $this->dispatch('order-payment-approved');
    }

    public function render()
    {
        return view('livewire.admin.counter-payment-recording', [
            'bankChannels' => PaymentMethods::bankChannels(),
        ]);
    }
}
