@extends('storefront.layout')

@section('title', 'Pay for your order — Othbar')

@section('content')

<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 4rem 0 3rem;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <p class="section-label">Payment</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Complete your payment</h1>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 4rem 2rem;">

    @if($errors->any())
    <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem;">
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 380px; gap: 3rem; align-items: start;">

        <div>
            <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2rem; margin-bottom: 2rem;">
                <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.5); margin-bottom: 0.5rem;">Pay for</p>
                <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;">Order {{ $order->number }}</p>
                <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-top: 0.5rem;">Transfer exactly <strong style="color: #C4843C;">Nu. {{ number_format($totalNu) }}</strong> and include your order number in the transfer note if your app allows.</p>
            </div>

            <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 1rem;">Bank &amp; QR</h2>
            <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-bottom: 1.5rem; line-height: 1.7;">Choose your channel, scan the QR with the matching app, then upload your payment screenshot below.</p>

            <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2rem; margin-bottom: 2rem;">
                <div style="display: flex; gap: 0; margin-bottom: 1.75rem; border-bottom: 2px solid #D8CCAD;">
                    @foreach($paymentChannels as $i => $ch)
                    <button type="button" id="pay-tab-{{ $ch['id'] }}"
                        onclick="paySwitchBank('{{ $ch['id'] }}')"
                        style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 2px solid {{ $i === 0 ? '#1E3A2A' : 'transparent' }}; margin-bottom: -2px; font-family: 'Jost', sans-serif; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: {{ $i === 0 ? '#1E3A2A' : 'rgba(30,58,42,0.4)' }}; cursor: pointer;">
                        {{ $ch['label'] }}
                    </button>
                    @endforeach
                </div>

                @foreach($paymentChannels as $i => $ch)
                @php($qrFull = public_path($ch['qr_public_path']))
                <div id="pay-bank-{{ $ch['id'] }}" class="pay-bank-panel" style="{{ $i > 0 ? 'display:none' : '' }}">
                    <div style="display: grid; grid-template-columns: 1fr 160px; gap: 2rem; align-items: start;">
                        <div>
                            <div style="margin-bottom: 1rem;">
                                <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Bank</p>
                                <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">{{ $ch['bank_label'] }}</p>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account name</p>
                                <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 600;">{{ $ch['account_name'] }}</p>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account number</p>
                                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                    <p id="pay-acc-{{ $ch['id'] }}" style="font-size: 1.1rem; color: #1E3A2A; font-weight: 700; letter-spacing: 0.05em; font-family: monospace;">{{ $ch['account_number'] }}</p>
                                    <button type="button" onclick="payCopyText('pay-acc-{{ $ch['id'] }}', this)" style="font-size: 0.65rem; padding: 0.25rem 0.6rem; background: #1E3A2A; color: #F7F2E8; border: none; cursor: pointer; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Copy</button>
                                </div>
                            </div>
                            <div>
                                <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Amount</p>
                                <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 700; color: #C4843C;">Nu. {{ number_format($totalNu) }}</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                            @if(file_exists($qrFull))
                            <div style="width: 140px; height: 140px; background: #fff; display: flex; align-items: center; justify-content: center; padding: 8px; border: 1px solid #D8CCAD;">
                                <img src="{{ asset($ch['qr_public_path']) }}" alt="{{ $ch['label'] }} QR" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                            @else
                            <div style="width: 140px; height: 140px; background: #EDE5D0; border: 2px dashed #D8CCAD; display: flex; align-items: center; justify-content: center; padding: 0.75rem; text-align: center;">
                                <p style="font-size: 0.65rem; color: rgba(30,58,42,0.55); line-height: 1.4;">Add QR image:<br><code style="font-size:0.6rem;">public/{{ $ch['qr_public_path'] }}</code></p>
                            </div>
                            @endif
                            <p style="font-size: 0.65rem; color: rgba(30,58,42,0.5); text-align: center;">Scan with {{ $ch['label'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <form method="POST" action="{{ route('checkout.pay.submit', ['order' => $order->id, 'token' => $token]) }}" enctype="multipart/form-data">
                @csrf
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

                <button type="submit" class="btn-primary" style="margin-top: 1.5rem; width: 100%; max-width: 320px; display: inline-flex; justify-content: center; border: none; cursor: pointer; font-size: 0.85rem; box-sizing: border-box;">
                    Submit payment proof
                </button>
            </form>
        </div>

        <div style="background: #EDE5D0; padding: 2rem; border: 1px solid #D8CCAD; position: sticky; top: 100px;">
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; color: #1E3A2A; margin-bottom: 1rem;">Order summary</h3>
            @foreach($order->items as $item)
            <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(216,204,173,0.5); font-size: 0.85rem;">
                <span>{{ $item->name }} × {{ $item->quantity }}</span>
                <span>Nu. {{ number_format($item->line_total_minor / 100) }}</span>
            </div>
            @endforeach
            <div style="display: flex; justify-content: space-between; padding-top: 1rem; margin-top: 0.5rem; border-top: 2px solid #1E3A2A; font-weight: 600;">
                <span>Total</span>
                <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.25rem;">Nu. {{ number_format($totalNu) }}</span>
            </div>
            <p style="font-size: 0.72rem; color: rgba(30,58,42,0.55); margin-top: 1.25rem; line-height: 1.6;">Fulfillment: <strong>{{ $order->fulfillment_method === 'pickup' ? 'In-store pickup' : 'Delivery' }}</strong></p>
        </div>
    </div>
</div>

<script>
const payChannelIds = @json(array_column($paymentChannels, 'id'));
function paySwitchBank(id) {
    document.querySelectorAll('.pay-bank-panel').forEach(p => p.style.display = 'none');
    const panel = document.getElementById('pay-bank-' + id);
    if (panel) panel.style.display = 'block';
    payChannelIds.forEach(b => {
        const tab = document.getElementById('pay-tab-' + b);
        if (!tab) return;
        if (b === id) {
            tab.style.borderBottomColor = '#1E3A2A';
            tab.style.color = '#1E3A2A';
        } else {
            tab.style.borderBottomColor = 'transparent';
            tab.style.color = 'rgba(30,58,42,0.4)';
        }
    });
}
function payCopyText(elementId, btn) {
    const text = document.getElementById(elementId).textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.style.background = '#C4843C';
        setTimeout(() => { btn.textContent = orig; btn.style.background = '#1E3A2A'; }, 2000);
    });
}
</script>

@endsection
