@php
    use App\Support\PaymentMethods;

    $metadata = $order->metadata ?? [];
    $paymentMethod = $metadata['payment_method'] ?? null;
    $paymentBank = $metadata['payment_bank'] ?? null;
@endphp

<div class="oth-payment">
    @if (session('payment_verified'))
        <div class="oth-payment__notice oth-payment__notice--success" style="margin-bottom:1rem;">
            {{ session('payment_verified') }}
        </div>
    @endif

    @if($order->payment_status === \App\Enums\PaymentStatus::Paid)
        <div class="oth-payment__status oth-payment__status--paid">
            <strong>Payment recorded</strong>
            <p class="oth-payment__meta">{{ PaymentMethods::paymentSummary($metadata, $order->payment_reference) }}</p>
        </div>
    @else
        <form wire:submit="recordPaymentAndFulfill" class="oth-payment__form">
            <div style="display:grid;gap:0.75rem;">
                <label class="oth-payment__field">
                    <span class="oth-payment__label">Payment mode</span>
                    <select wire:model.live="payment_method" class="oth-payment__input">
                        @foreach(PaymentMethods::modeLabels() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                @if($payment_method === PaymentMethods::MODE_BANK_TRANSFER)
                    <label class="oth-payment__field">
                        <span class="oth-payment__label">Mobile banking app</span>
                        <select wire:model="payment_bank" class="oth-payment__input" required>
                            <option value="">Select app…</option>
                            @foreach($bankChannels as $bank)
                                <option value="{{ $bank['id'] }}">{{ $bank['label'] }} — {{ $bank['bank_label'] }}</option>
                            @endforeach
                        </select>
                        @error('payment_bank')
                            <span class="oth-payment__error">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="oth-payment__field">
                        <span class="oth-payment__label">Transaction ref / journal number</span>
                        <input
                            type="text"
                            wire:model="payment_reference"
                            class="oth-payment__input"
                            placeholder="e.g. JRN-2026-00123"
                            required
                        />
                        @error('payment_reference')
                            <span class="oth-payment__error">{{ $message }}</span>
                        @enderror
                    </label>
                @endif
            </div>

            <div class="oth-payment__actions" style="margin-top:1rem;display:flex;flex-wrap:wrap;gap:0.5rem;">
                <button type="submit" class="oth-btn oth-btn--success" wire:loading.attr="disabled">
                    Record payment &amp; fulfill
                </button>
                <button type="button" class="oth-btn oth-btn--secondary" wire:click="recordPayment" wire:loading.attr="disabled">
                    Record payment only
                </button>
            </div>
        </form>
    @endif
</div>
