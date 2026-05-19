@php
    $proofDialogId = 'oth-proof-dialog-'.$order->id;
    $approveDialogId = 'oth-approve-dialog-'.$order->id;
    $rejectDialogId = 'oth-reject-dialog-'.$order->id;
@endphp

<div class="oth-payment">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:0.5rem;margin-bottom:0.75rem;">
        <span style="font-size:0.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Status</span>
        <x-filament::badge size="sm" :color="$order->payment_status->getColor()" :icon="$order->payment_status->getIcon()">
            {{ $order->payment_status->getLabel() }}
        </x-filament::badge>
    </div>

    @if(session('payment_verified'))
    <div class="oth-alert oth-alert--success">{{ session('payment_verified') }}</div>
    @endif
    @if(session('payment_rejected'))
    <div class="oth-alert oth-alert--danger">{{ session('payment_rejected') }}</div>
    @endif

    @php
        use App\Support\PaymentMethods;

        $meta = is_array($order->metadata) ? $order->metadata : (json_decode((string) $order->metadata, true) ?: []);
        $customerEmail = $meta['email'] ?? null;
        $couponCode = $meta['coupon_code'] ?? null;
        $paymentBank = $meta['payment_bank'] ?? null;
    @endphp

    <div class="oth-payment__row">
        <span class="oth-payment__label">Method</span>
        <span class="oth-payment__value">{{ PaymentMethods::paymentSummary($meta, $order->payment_reference) }}</span>
    </div>

    @if($paymentBank)
    <div class="oth-payment__row">
        <span class="oth-payment__label">App used</span>
        <span class="oth-payment__value">{{ PaymentMethods::bankDisplayLabel($paymentBank) }}</span>
    </div>
    @endif

    @if(filled($merchantAccount['account_number'] ?? null))
    <div class="oth-payment__banks">
        <p style="margin:0 0 0.5rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Receiving account</p>
        <div class="oth-payment__row" style="padding:0.25rem 0;">
            <span class="oth-payment__label">Account</span>
            <span class="oth-payment__value" style="font-size:0.75rem;">
                {{ $merchantAccount['bank_label'] }}<br>
                <span style="font-family:monospace;">{{ $merchantAccount['account_number'] }}</span>
            </span>
        </div>
    </div>
    @endif

    @if($order->payment_reference)
    <div class="oth-payment__row">
        <span class="oth-payment__label">Reference</span>
        <span class="oth-payment__value" style="font-family:monospace;font-size:0.75rem;">{{ $order->payment_reference }}</span>
    </div>
    @endif

    @if($customerEmail)
    <div class="oth-payment__row">
        <span class="oth-payment__label">Email</span>
        <span class="oth-payment__value"><a href="mailto:{{ $customerEmail }}">{{ $customerEmail }}</a></span>
    </div>
    @endif

    @if($couponCode)
    <div class="oth-payment__row">
        <span class="oth-payment__label">Coupon</span>
        <span class="oth-payment__value"><code>{{ $couponCode }}</code></span>
    </div>
    @endif

    <hr class="oth-divider">

    <p style="margin:0 0 0.5rem;font-size:0.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;color:#6b7280;">Payment screenshot</p>

    @if($order->payment_proof_path)
        @if($proofIsImage)
        <button type="button" class="oth-proof-thumb"
            onclick="document.getElementById('{{ $proofDialogId }}').showModal()"
            title="Click to view full size">
            <img src="{{ $proofUrl }}" alt="Payment proof thumbnail"
                 onerror="this.parentElement.innerHTML='<span class=\'oth-proof-thumb__error\'>Unavailable</span>'">
            <span class="oth-proof-thumb__overlay">View</span>
        </button>

        <dialog id="{{ $proofDialogId }}" class="oth-proof-dialog"
            onclick="if (event.target === this) this.close()">
            <div class="oth-proof-dialog__inner" onclick="event.stopPropagation()">
                <div class="oth-dialog__header">
                    <h4 class="oth-dialog__title">Payment screenshot</h4>
                    <button type="button" class="oth-dialog__close"
                        onclick="document.getElementById('{{ $proofDialogId }}').close()"
                        aria-label="Close">&times;</button>
                </div>
                <div class="oth-dialog__body">
                    <img src="{{ $proofUrl }}" alt="Payment proof" class="oth-dialog__img">
                </div>
                <div class="oth-dialog__footer">
                    <button type="button" class="oth-btn oth-btn--secondary oth-btn--sm"
                        onclick="document.getElementById('{{ $proofDialogId }}').close()">Close</button>
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="oth-btn oth-btn--secondary oth-btn--sm">Open in tab</a>
                    <a href="{{ $proofDownloadUrl }}" class="oth-btn oth-btn--primary oth-btn--sm">Download</a>
                </div>
            </div>
        </dialog>
        @else
        <p style="margin:0;font-size:0.875rem;"><strong>{{ basename($order->payment_proof_path) }}</strong> (PDF)</p>
        <div class="oth-proof-actions">
            <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="oth-btn oth-btn--secondary oth-btn--sm">View</a>
            <a href="{{ $proofDownloadUrl }}" class="oth-btn oth-btn--primary oth-btn--sm">Download</a>
        </div>
        @endif
    @else
    <p class="oth-order-meta" style="margin:0;">No screenshot uploaded yet.</p>
    @endif

    <hr class="oth-divider">

    @if($order->payment_status === \App\Enums\PaymentStatus::Pending)
    <div class="oth-grid-2">
        @if($order->payment_proof_path)
        <button type="button"
            onclick="document.getElementById(@js($approveDialogId)).showModal()"
            class="oth-btn oth-btn--success oth-btn--sm">
            Approve
        </button>
        @else
        <div class="oth-alert oth-alert--warning" style="grid-column:1/-1;margin:0;">Upload required before approval.</div>
        @endif
        <button type="button"
            onclick="document.getElementById(@js($rejectDialogId)).showModal()"
            class="oth-btn oth-btn--danger oth-btn--sm"
            @if(!$order->payment_proof_path) style="grid-column:1/-1" @endif>
            Reject
        </button>
    </div>

    <x-oth-confirm-dialog
        :id="$approveDialogId"
        title="Approve payment"
        message="Confirm this bank transfer payment? The order will be marked as payment confirmed and ready for fulfillment."
        confirm-label="Approve payment"
        confirm-variant="success"
        confirm-target="approve"
        confirm-loading-target="approve"
    />

    <x-oth-confirm-dialog
        :id="$rejectDialogId"
        title="Reject payment"
        message="Reject this payment proof? The payment will be voided and the order will be cancelled."
        confirm-label="Reject payment"
        confirm-variant="danger"
        confirm-target="reject"
        confirm-loading-target="reject"
    />

    @elseif($order->payment_status === \App\Enums\PaymentStatus::Paid)
    <div class="oth-alert oth-alert--success" style="margin:0;">Payment confirmed. Mark the order fulfilled when delivery or pickup is complete.</div>

    @elseif($order->payment_status === \App\Enums\PaymentStatus::Voided)
    <div class="oth-alert oth-alert--danger" style="margin:0;">Payment rejected. Order cancelled.</div>
    @endif
</div>
