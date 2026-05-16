<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class PaymentVerification extends Component
{
    public Order $order;

    public bool $showConfirmApprove = false;

    public bool $showConfirmReject = false;

    public function mount(Order $order): void
    {
        $this->order = $order;
    }

    public function approve(): void
    {
        if (! $this->order->payment_proof_path) {
            return;
        }

        if ($this->order->payment_status !== PaymentStatus::Pending) {
            return;
        }

        $this->order->update([
            'payment_status' => PaymentStatus::Paid,
            'status' => OrderStatus::Processing,
        ]);

        $this->order->refresh();
        $this->showConfirmApprove = false;

        session()->flash('payment_verified', 'Payment approved. Order is now processing.');
        $this->dispatch('order-payment-approved');
    }

    public function reject(): void
    {
        if ($this->order->payment_status !== PaymentStatus::Pending) {
            return;
        }

        $this->order->update([
            'payment_status' => PaymentStatus::Voided,
            'status' => OrderStatus::Cancelled,
        ]);

        $this->order->refresh();
        $this->showConfirmReject = false;

        session()->flash('payment_rejected', 'Payment rejected. Order has been cancelled.');
        $this->dispatch('order-payment-rejected');
    }

    public function proofUrl(): ?string
    {
        if (! $this->order->payment_proof_path) {
            return null;
        }

        return Storage::disk('public')->url($this->order->payment_proof_path);
    }

    public function proofIsImage(): bool
    {
        if (! $this->order->payment_proof_path) {
            return false;
        }

        $ext = strtolower(pathinfo($this->order->payment_proof_path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    public function render()
    {
        return view('livewire.admin.payment-verification', [
            'proofUrl' => $this->proofUrl(),
            'proofIsImage' => $this->proofIsImage(),
        ]);
    }
}
