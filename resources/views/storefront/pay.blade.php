@extends('storefront.layout')

@section('title', 'Pay for your order — Othbar')

@section('content')

<div class="sf-page-header" style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD;">
    <div class="sf-container">
        <p class="section-label">Payment</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Complete your payment</h1>
    </div>
</div>

<div class="sf-container sf-page-body">

    @if($errors->any())
    <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem;">
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="sf-grid-split">

        <div>
            <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2rem; margin-bottom: 2rem;">
                <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.5); margin-bottom: 0.5rem;">Pay for</p>
                <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;">Order {{ $order->number }}</p>
                <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-top: 0.5rem;">Transfer exactly <strong style="color: #C4843C;">Nu. {{ number_format($totalNu) }}</strong> to our account below using your mobile banking app.</p>
            </div>

            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 0.5rem;">Our bank account &amp; QR</h2>
            <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-bottom: 1.5rem; line-height: 1.7;">
                Transfer to this single Othbar account. You can pay from any supported app:
                <strong>{{ collect($paymentApps)->pluck('label')->implode(', ') }}</strong>.
            </p>

            <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2rem; margin-bottom: 2rem;">
                <div class="sf-pay-bank-grid">
                    <div>
                        <div style="margin-bottom: 1rem;">
                            <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Bank</p>
                            <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">{{ $merchantAccount['bank_label'] }}</p>
                        </div>
                        @if(filled($merchantAccount['account_name'] ?? null))
                        <div style="margin-bottom: 1rem;">
                            <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account name</p>
                            <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">{{ $merchantAccount['account_name'] }}</p>
                        </div>
                        @endif
                        <div style="margin-bottom: 1rem;">
                            <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account number</p>
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <p id="pay-acc-number" style="font-size: 1.1rem; color: #1E3A2A; font-weight: 700; letter-spacing: 0.05em; font-family: monospace;">{{ $merchantAccount['account_number'] }}</p>
                                <button type="button" onclick="payCopyText('pay-acc-number', this)" style="font-size: 0.65rem; padding: 0.25rem 0.6rem; background: #1E3A2A; color: #F7F2E8; border: none; cursor: pointer; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Copy</button>
                            </div>
                        </div>
                        <div>
                            <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Amount</p>
                            <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 700; color: #C4843C;">Nu. {{ number_format($totalNu) }}</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                        @if(!empty($merchantAccount['qr_url']))
                        <div style="width: 140px; height: 140px; background: #fff; display: flex; align-items: center; justify-content: center; padding: 8px; border: 1px solid #D8CCAD;">
                            <img src="{{ $merchantAccount['qr_url'] }}" alt="Payment QR" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                        @else
                        <div style="width: 140px; height: 140px; background: #EDE5D0; border: 2px dashed #D8CCAD; display: flex; align-items: center; justify-content: center; padding: 0.75rem; text-align: center;">
                            <p style="font-size: 0.65rem; color: rgba(30,58,42,0.55); line-height: 1.4;">Upload a QR in <strong>Admin → Payment &amp; GST</strong>.</p>
                        </div>
                        @endif
                        <p style="font-size: 0.65rem; color: rgba(30,58,42,0.5); text-align: center;">Scan with your banking app</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('checkout.pay.submit', ['order' => $order->id, 'token' => $token]) }}" enctype="multipart/form-data">
                @csrf

                <div style="border: 1px solid {{ $errors->has('payment_reference') || $errors->has('payment_bank') ? '#b91c1c' : '#D8CCAD' }}; padding: 1.75rem; background: #EDE5D0; margin-bottom: 1.5rem;">
                    <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.35rem;">Which app did you pay with?</p>
                    <p style="font-size: 0.82rem; color: rgba(30,58,42,0.65); margin-bottom: 1.25rem; line-height: 1.7;">
                        Select the mobile banking app you used to send this transfer.
                    </p>

                    <div class="sf-pay-app-grid">
                        @foreach($paymentApps as $app)
                        <label class="sf-pay-app-option {{ old('payment_bank') === $app['id'] ? 'is-selected' : '' }}">
                            <input type="radio" name="payment_bank" value="{{ $app['id'] }}" {{ old('payment_bank') === $app['id'] ? 'checked' : '' }} required>
                            <span class="sf-pay-app-option__label">{{ $app['label'] }}</span>
                            <span class="sf-pay-app-option__bank">{{ $app['bank_label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('payment_bank')
                    <p style="font-size: 0.78rem; color: #b91c1c; margin-top: 0.75rem;">{{ $message }}</p>
                    @enderror

                    <label style="display: block; margin: 1.25rem 0 0.35rem; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(30,58,42,0.55);">
                        Transaction ref / journal number
                    </label>
                    <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required maxlength="255"
                        placeholder="e.g. JRN-2026-00123"
                        style="width: 100%; box-sizing: border-box; padding: 0.75rem 1rem; border: 1px solid {{ $errors->has('payment_reference') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A;">
                    @error('payment_reference')
                    <p style="font-size: 0.78rem; color: #b91c1c; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="border: 1px solid {{ $errors->has('payment_proof') ? '#b91c1c' : '#D8CCAD' }}; padding: 1.75rem; background: #EDE5D0;">
                    <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 1rem;">Upload payment screenshot</p>
                    <p style="font-size: 0.82rem; color: rgba(30,58,42,0.65); margin-bottom: 1.25rem; line-height: 1.7;">
                        A clear screenshot or PDF of the successful payment is required. Our team will verify it before processing your order.
                    </p>
                    <label style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border: 2px dashed #D8CCAD; background: #F7F2E8; cursor: pointer;"
                        onmouseover="this.style.borderColor='#1E3A2A'" onmouseout="this.style.borderColor='#D8CCAD'">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(30,58,42,0.5)" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div>
                            <p id="pay-upload-text" style="font-size: 0.85rem; color: #1E3A2A; font-weight: 500;">Click to choose file</p>
                            <p style="font-size: 0.72rem; color: rgba(30,58,42,0.5); margin-top: 0.2rem;">JPG, PNG or PDF — max 5 MB</p>
                        </div>
                        <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf" required style="display: none;"
                            onchange="document.getElementById('pay-upload-text').textContent = this.files[0] ? this.files[0].name : 'Click to choose file'">
                    </label>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 1.5rem; width: 100%; display: flex; justify-content: center; border: none; cursor: pointer; font-size: 0.85rem; box-sizing: border-box;">
                    Submit payment proof
                </button>
            </form>
        </div>

        <div class="sf-sticky-summary" style="background: #EDE5D0; padding: 2rem; border: 1px solid #D8CCAD;">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; color: #1E3A2A; margin-bottom: 1rem;">Order summary</h3>
            @php $pricing = $order->pricingSummary(); @endphp
            @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(216,204,173,0.5); font-size: 0.85rem;">
                <span>{{ $item->name }} × {{ $item->quantity }}</span>
                <span>Nu. {{ number_format($item->line_total_minor / 100) }}</span>
            </div>
            @endforeach
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; margin-top: 0.5rem; font-size: 0.82rem; border-top: 1px solid rgba(216,204,173,0.5);">
                <span>Subtotal</span>
                <span>Nu. {{ number_format($pricing['subtotal_minor'] / 100) }}</span>
            </div>
            @if($pricing['discount_minor'] > 0)
            <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.82rem;">
                <span>Discount</span>
                <span style="color: #C4843C;">− Nu. {{ number_format($pricing['discount_minor'] / 100) }}</span>
            </div>
            @endif
            @if($pricing['gst_minor'] > 0)
            <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; font-size: 0.82rem;">
                <span>GST ({{ rtrim(rtrim(number_format($pricing['gst_percentage'], 2), '0'), '.') }}%)</span>
                <span>Nu. {{ number_format($pricing['gst_minor'] / 100) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding-top: 1rem; margin-top: 0.5rem; border-top: 2px solid #1E3A2A; font-weight: 600;">
                <span>Total</span>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem;">Nu. {{ number_format($totalNu) }}</span>
            </div>
            <p style="font-size: 0.72rem; color: rgba(30,58,42,0.55); margin-top: 1.25rem; line-height: 1.6;">Fulfillment: <strong>{{ $order->fulfillment_method === 'pickup' ? 'In-store pickup' : 'Delivery' }}</strong></p>
        </div>
    </div>
</div>

<style>
.sf-pay-app-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}
@media (min-width: 640px) {
    .sf-pay-app-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
.sf-pay-app-option {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    padding: 0.85rem 1rem;
    border: 1px solid #D8CCAD;
    background: #F7F2E8;
    cursor: pointer;
    transition: border-color 0.15s ease, background 0.15s ease;
}
.sf-pay-app-option input { position: absolute; opacity: 0; pointer-events: none; }
.sf-pay-app-option__label { font-size: 0.9rem; font-weight: 700; color: #1E3A2A; }
.sf-pay-app-option__bank { font-size: 0.72rem; color: rgba(30,58,42,0.6); line-height: 1.35; }
.sf-pay-app-option:hover { border-color: #1E3A2A; }
.sf-pay-app-option:has(input:checked),
.sf-pay-app-option.is-selected {
    border-color: #1E3A2A;
    background: #EDE5D0;
    box-shadow: inset 0 0 0 1px #1E3A2A;
}
</style>

<script>
function payCopyText(elementId, btn) {
    const text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.background = '#C4843C';
        setTimeout(() => { btn.textContent = orig; btn.style.background = '#1E3A2A'; }, 2000);
    });
}
document.querySelectorAll('.sf-pay-app-option input').forEach(function (input) {
    input.addEventListener('change', function () {
        document.querySelectorAll('.sf-pay-app-option').forEach(function (label) {
            label.classList.toggle('is-selected', label.querySelector('input')?.checked);
        });
    });
});
</script>

@endsection
