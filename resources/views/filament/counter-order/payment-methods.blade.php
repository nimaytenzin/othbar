@php
    use App\Support\PaymentMethods;

    /** @var string|null $selected */
    /** @var string|null $selectedBank */
    $methods = PaymentMethods::modeLabels();
    $banks = PaymentMethods::bankChannels();
    $isBankTransfer = ($selected ?? PaymentMethods::MODE_CASH) === PaymentMethods::MODE_BANK_TRANSFER;
@endphp

<div class="oth-payment-methods oth-payment-methods--modes">
    @foreach($methods as $value => $label)
        <button
            type="button"
            class="oth-payment-method {{ ($selected ?? PaymentMethods::MODE_CASH) === $value ? 'is-active' : '' }}"
            @if($value === PaymentMethods::MODE_CASH)
                wire:click="$set('data.payment_method', '{{ $value }}'); $set('data.payment_bank', null); $set('data.payment_reference', null)"
            @else
                wire:click="$set('data.payment_method', '{{ $value }}')"
            @endif
        >
            <span class="oth-payment-method__label">{{ $label }}</span>
            <span class="oth-payment-method__hint">
                @if($value === PaymentMethods::MODE_CASH)
                    Paid at counter
                @else
                    {{ PaymentMethods::paymentAppNames() }}
                @endif
            </span>
        </button>
    @endforeach
</div>

@if($isBankTransfer)
    <div class="oth-payment-banks">
        <p class="oth-payment-banks__label">Select mobile banking app</p>
        <div class="oth-payment-methods oth-payment-methods--banks">
            @foreach($banks as $bank)
                <button
                    type="button"
                    class="oth-payment-method oth-payment-method--bank {{ ($selectedBank ?? '') === $bank['id'] ? 'is-active' : '' }}"
                    wire:click="$set('data.payment_bank', '{{ $bank['id'] }}')"
                >
                    <span class="oth-payment-method__label">{{ $bank['label'] }}</span>
                    @if(filled($bank['bank_label'] ?? null))
                        <span class="oth-payment-method__hint">{{ $bank['bank_label'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
@endif
