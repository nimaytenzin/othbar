@extends('storefront.layout')

@section('title', 'Checkout — Othbar')

@section('content')

<div style="background: #EDE5D0; border-bottom: 1px solid #D8CCAD; padding: 4rem 0 3rem;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <p class="section-label">Almost there</p>
        <h1 style="font-family: 'Cormorant Garamond', serif; font-size: clamp(2.5rem, 5vw, 4rem); color: #1E3A2A; margin-top: 0.5rem;">Checkout</h1>
    </div>
</div>

<div style="max-width: 1280px; margin: 0 auto; padding: 4rem 2rem;">

    @if($errors->any())
    <div style="background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24; padding: 0.875rem 1.25rem; margin-bottom: 2rem; font-size: 0.875rem; border-radius: 0;">
        <ul style="margin: 0; padding-left: 1.25rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}" enctype="multipart/form-data">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 4rem; align-items: start;">

            {{-- Left column --}}
            <div>

                {{-- ── Delivery details ── --}}
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 2rem;">1. Delivery details</h2>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">First name *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('first_name') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Last name *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('last_name') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Phone *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="+975 17 123 456"
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('phone') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="For order updates (optional)"
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #D8CCAD; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Street address *</label>
                    <input type="text" name="street_address" value="{{ old('street_address') }}" required placeholder="House/block number, street name"
                        style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('street_address') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                        onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">City / Dzongkhag *</label>
                        <input type="text" name="city" value="{{ old('city', 'Thimphu') }}" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('city') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Postal code *</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="e.g. 11001" required
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid {{ $errors->has('postal_code') ? '#b91c1c' : '#D8CCAD' }}; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

                <div style="margin-bottom: 2.5rem;">
                    <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.5rem;">Delivery notes</label>
                    <textarea name="notes" rows="2" placeholder="Special delivery instructions, landmarks..."
                        style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #D8CCAD; background: #F7F2E8; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box; resize: vertical;"
                        onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">{{ old('notes') }}</textarea>
                </div>

                <div class="gold-line" style="margin-bottom: 2.5rem;"></div>

                {{-- ── Payment — Scan to Pay ── --}}
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; color: #1E3A2A; margin-bottom: 0.5rem;">2. Payment</h2>
                <p style="font-size: 0.85rem; color: rgba(30,58,42,0.65); margin-bottom: 2rem; line-height: 1.7;">
                    Transfer the exact order amount to our bank account using MBOB, EPAY, or DKBANK, then confirm below.
                </p>

                {{-- Bank details card --}}
                <div style="background: #F7F2E8; border: 1px solid #D8CCAD; padding: 2rem; margin-bottom: 2rem;">

                    {{-- Bank tabs --}}
                    <div style="display: flex; gap: 0; margin-bottom: 1.75rem; border-bottom: 2px solid #D8CCAD;">
                        @foreach([
                            ['id' => 'mbob',  'label' => 'MBOB',   'bank' => 'Bank of Bhutan'],
                            ['id' => 'epay',  'label' => 'EPAY',   'bank' => 'Bhutan National Bank'],
                            ['id' => 'dkbank','label' => 'DKBANK', 'bank' => 'Druk PNB Bank'],
                        ] as $i => $tab)
                        <button type="button" id="tab-{{ $tab['id'] }}"
                            onclick="switchBank('{{ $tab['id'] }}')"
                            style="padding: 0.75rem 1.5rem; background: none; border: none; border-bottom: 2px solid {{ $i === 0 ? '#1E3A2A' : 'transparent' }}; margin-bottom: -2px; font-family: 'Jost', sans-serif; font-size: 0.78rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: {{ $i === 0 ? '#1E3A2A' : 'rgba(30,58,42,0.4)' }}; cursor: pointer; transition: all 0.2s;">
                            {{ $tab['label'] }}
                        </button>
                        @endforeach
                    </div>

                    {{-- MBOB details --}}
                    <div id="bank-mbob" class="bank-panel">
                        <div style="display: grid; grid-template-columns: 1fr 140px; gap: 2rem; align-items: start;">
                            <div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Bank</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">Bank of Bhutan (BOB)</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account name</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 600;">Othbar Horticulture Project</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account number</p>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <p id="acc-mbob" style="font-size: 1.1rem; color: #1E3A2A; font-weight: 700; letter-spacing: 0.05em; font-family: monospace;">10101-00123456-78</p>
                                        <button type="button" onclick="copyText('acc-mbob', this)" style="font-size: 0.65rem; padding: 0.25rem 0.6rem; background: #1E3A2A; color: #F7F2E8; border: none; cursor: pointer; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Transfer amount</p>
                                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 700; color: #C4843C;">Nu. {{ number_format($cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity) / 100) }}</p>
                                </div>
                            </div>
                            {{-- QR placeholder --}}
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <div style="width: 120px; height: 120px; background: #1E3A2A; display: flex; align-items: center; justify-content: center; padding: 10px;">
                                    <svg viewBox="0 0 100 100" width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <!-- QR code SVG pattern (decorative) -->
                                        <rect width="100" height="100" fill="#1E3A2A"/>
                                        <rect x="5" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="60" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="66" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="71" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="5" y="60" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="66" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="71" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="48" y="48" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="58" y="48" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="68" y="48" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="78" y="48" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="88" y="48" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="48" y="58" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="68" y="58" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="88" y="58" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="48" y="68" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="58" y="68" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="68" y="68" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="48" y="78" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="78" y="78" width="7" height="7" fill="#F7F2E8"/>
                                        <rect x="88" y="88" width="7" height="7" fill="#F7F2E8"/>
                                    </svg>
                                </div>
                                <p style="font-size: 0.6rem; color: rgba(30,58,42,0.5); text-align: center; letter-spacing: 0.05em;">Scan with MBOB</p>
                            </div>
                        </div>
                    </div>

                    {{-- EPAY details --}}
                    <div id="bank-epay" class="bank-panel" style="display:none;">
                        <div style="display: grid; grid-template-columns: 1fr 140px; gap: 2rem; align-items: start;">
                            <div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Bank</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">Bhutan National Bank (BNB)</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account name</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 600;">Othbar Horticulture Project</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account number</p>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <p id="acc-epay" style="font-size: 1.1rem; color: #1E3A2A; font-weight: 700; letter-spacing: 0.05em; font-family: monospace;">201-00-987654-3</p>
                                        <button type="button" onclick="copyText('acc-epay', this)" style="font-size: 0.65rem; padding: 0.25rem 0.6rem; background: #1E3A2A; color: #F7F2E8; border: none; cursor: pointer; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Transfer amount</p>
                                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 700; color: #C4843C;">Nu. {{ number_format($cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity) / 100) }}</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <div style="width: 120px; height: 120px; background: #1E3A2A; display: flex; align-items: center; justify-content: center; padding: 10px;">
                                    <svg viewBox="0 0 100 100" width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="100" fill="#1E3A2A"/>
                                        <rect x="5" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="60" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="66" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="71" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="5" y="60" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="66" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="71" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="55" y="55" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="67" y="55" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="79" y="55" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="55" y="67" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="79" y="67" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="55" y="79" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="67" y="79" width="9" height="9" fill="#F7F2E8"/>
                                        <rect x="79" y="79" width="9" height="9" fill="#F7F2E8"/>
                                    </svg>
                                </div>
                                <p style="font-size: 0.6rem; color: rgba(30,58,42,0.5); text-align: center; letter-spacing: 0.05em;">Scan with EPAY</p>
                            </div>
                        </div>
                    </div>

                    {{-- DKBANK details --}}
                    <div id="bank-dkbank" class="bank-panel" style="display:none;">
                        <div style="display: grid; grid-template-columns: 1fr 140px; gap: 2rem; align-items: start;">
                            <div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Bank</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 500;">Druk PNB Bank Ltd</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account name</p>
                                    <p style="font-size: 0.95rem; color: #1E3A2A; font-weight: 600;">Othbar Horticulture Project</p>
                                </div>
                                <div style="margin-bottom: 1rem;">
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Account number</p>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <p id="acc-dkbank" style="font-size: 1.1rem; color: #1E3A2A; font-weight: 700; letter-spacing: 0.05em; font-family: monospace;">DK-0042-00543210</p>
                                        <button type="button" onclick="copyText('acc-dkbank', this)" style="font-size: 0.65rem; padding: 0.25rem 0.6rem; background: #1E3A2A; color: #F7F2E8; border: none; cursor: pointer; letter-spacing: 0.08em; text-transform: uppercase; font-weight: 600;">Copy</button>
                                    </div>
                                </div>
                                <div>
                                    <p style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: rgba(30,58,42,0.45); margin-bottom: 0.3rem;">Transfer amount</p>
                                    <p style="font-family: 'Cormorant Garamond', serif; font-size: 1.4rem; font-weight: 700; color: #C4843C;">Nu. {{ number_format($cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity) / 100) }}</p>
                                </div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                <div style="width: 120px; height: 120px; background: #1E3A2A; display: flex; align-items: center; justify-content: center; padding: 10px;">
                                    <svg viewBox="0 0 100 100" width="100" height="100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="100" fill="#1E3A2A"/>
                                        <rect x="5" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="60" y="5" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="66" y="11" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="71" y="16" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="5" y="60" width="35" height="35" rx="2" fill="#F7F2E8"/>
                                        <rect x="11" y="66" width="23" height="23" rx="1" fill="#1E3A2A"/>
                                        <rect x="16" y="71" width="13" height="13" fill="#F7F2E8"/>
                                        <rect x="48" y="48" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="62" y="48" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="76" y="48" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="48" y="62" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="62" y="62" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="76" y="76" width="10" height="10" fill="#F7F2E8"/>
                                        <rect x="48" y="76" width="10" height="10" fill="#F7F2E8"/>
                                    </svg>
                                </div>
                                <p style="font-size: 0.6rem; color: rgba(30,58,42,0.5); text-align: center; letter-spacing: 0.05em;">Scan with DKBANK</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Confirm payment ── --}}
                <div style="border: 1px solid {{ $errors->has('payment_proof') || $errors->has('payment_reference') ? '#b91c1c' : '#D8CCAD' }}; padding: 1.75rem; background: #F7F2E8;">
                    <p style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 1.25rem;">Confirm your payment</p>
                    <p style="font-size: 0.82rem; color: rgba(30,58,42,0.65); margin-bottom: 1.5rem; line-height: 1.7;">
                        After transferring, provide either your payment screenshot <em>or</em> the transaction journal/reference number.
                    </p>

                    {{-- Option A: Upload screenshot --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.75rem;">
                            Option A — Upload payment screenshot
                        </label>
                        <label id="upload-label" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; border: 2px dashed #D8CCAD; background: #EDE5D0; cursor: pointer; transition: border-color 0.2s;"
                            onmouseover="this.style.borderColor='#1E3A2A'" onmouseout="this.style.borderColor='#D8CCAD'">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(30,58,42,0.5)" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <div>
                                <p id="upload-label-text" style="font-size: 0.85rem; color: #1E3A2A; font-weight: 500;">Click to upload screenshot</p>
                                <p style="font-size: 0.72rem; color: rgba(30,58,42,0.5); margin-top: 0.2rem;">JPG, PNG or PDF — max 5 MB</p>
                            </div>
                            <input type="file" name="payment_proof" id="payment_proof" accept=".jpg,.jpeg,.png,.pdf"
                                style="display: none;"
                                onchange="document.getElementById('upload-label-text').textContent = this.files[0] ? this.files[0].name : 'Click to upload screenshot';">
                        </label>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                        <div style="flex: 1; height: 1px; background: #D8CCAD;"></div>
                        <span style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(30,58,42,0.4);">or</span>
                        <div style="flex: 1; height: 1px; background: #D8CCAD;"></div>
                    </div>

                    {{-- Option B: Reference number --}}
                    <div>
                        <label style="display: block; font-size: 0.72rem; font-weight: 600; letter-spacing: 0.15em; text-transform: uppercase; color: #1E3A2A; margin-bottom: 0.75rem;">
                            Option B — Enter transaction reference / journal number
                        </label>
                        <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                            placeholder="e.g. TXN2026030601234 or journal no."
                            style="width: 100%; padding: 0.875rem 1rem; border: 1px solid #D8CCAD; background: #EDE5D0; font-family: 'Jost', sans-serif; font-size: 0.9rem; color: #1E3A2A; outline: none; box-sizing: border-box; letter-spacing: 0.02em;"
                            onfocus="this.style.borderColor='#1E3A2A'" onblur="this.style.borderColor='#D8CCAD'">
                    </div>
                </div>

            </div>

            {{-- ── Right: Order summary ── --}}
            <div style="background: #EDE5D0; padding: 2rem; border: 1px solid #D8CCAD; position: sticky; top: 100px;">
                <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 1.5rem; color: #1E3A2A; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #D8CCAD;">Your order</h2>

                <div style="margin-bottom: 1.25rem;">
                    @foreach($cartLines as $line)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid rgba(216,204,173,0.5);">
                        <div>
                            <p style="font-size: 0.88rem; color: #1E3A2A; font-weight: 500; line-height: 1.4;">{{ $line->purchasable->name ?? 'Product' }}</p>
                            <p style="font-size: 0.72rem; color: rgba(30,58,42,0.5); margin-top: 0.15rem;">Qty {{ $line->quantity }} &times; Nu. {{ number_format($line->unit_price_amount / 100) }}</p>
                        </div>
                        <span style="font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 600; color: #1E3A2A; white-space: nowrap; margin-left: 1rem;">Nu. {{ number_format(($line->unit_price_amount * $line->quantity) / 100) }}</span>
                    </div>
                    @endforeach
                </div>

                @if($cart && $cart->coupon_code)
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.82rem; color: #1E3A2A; border-bottom: 1px solid rgba(216,204,173,0.4);">
                    <span>Coupon: <strong>{{ $cart->coupon_code }}</strong></span>
                    <span style="color: #C4843C;">Applied</span>
                </div>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 2px solid #1E3A2A; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 1rem; font-weight: 600; color: #1E3A2A;">Total</span>
                    <span style="font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;">Nu. {{ number_format($cartLines->sum(fn($l) => $l->unit_price_amount * $l->quantity) / 100) }}</span>
                </div>

                {{-- Payment method badge --}}
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; background: #F7F2E8; border: 1px solid #D8CCAD; margin-bottom: 1.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1E3A2A" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <div>
                        <p style="font-size: 0.78rem; font-weight: 600; color: #1E3A2A;">Scan to Pay</p>
                        <p style="font-size: 0.7rem; color: rgba(30,58,42,0.5);">MBOB · EPAY · DKBANK</p>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; display: flex; justify-content: center; border: none; cursor: pointer; font-size: 0.85rem; box-sizing: border-box;">
                    Place order
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                </button>

                <p style="font-size: 0.7rem; color: rgba(30,58,42,0.45); text-align: center; margin-top: 1rem; line-height: 1.6;">
                    Your order will be confirmed once we verify your payment. This typically takes 1–2 hours.
                </p>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #D8CCAD;">
                    <a href="{{ route('cart') }}" style="font-size: 0.75rem; color: rgba(30,58,42,0.5); text-decoration: none; display: flex; align-items: center; gap: 0.4rem; justify-content: center;"
                        onmouseover="this.style.color='#1E3A2A'" onmouseout="this.style.color='rgba(30,58,42,0.5)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5m7-7-7 7 7 7"/></svg>
                        Edit basket
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function switchBank(id) {
    document.querySelectorAll('.bank-panel').forEach(p => p.style.display = 'none');
    document.getElementById('bank-' + id).style.display = 'block';

    ['mbob','epay','dkbank'].forEach(b => {
        const tab = document.getElementById('tab-' + b);
        if (b === id) {
            tab.style.borderBottomColor = '#1E3A2A';
            tab.style.color = '#1E3A2A';
        } else {
            tab.style.borderBottomColor = 'transparent';
            tab.style.color = 'rgba(30,58,42,0.4)';
        }
    });
}

function copyText(elementId, btn) {
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
