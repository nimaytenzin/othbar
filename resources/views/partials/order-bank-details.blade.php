@php
    /** @var array{bank_label: string, account_name: string, account_number: string, qr_url: ?string} $merchantAccount */
    /** @var list<array{id: string, label: string, bank_label: string}> $paymentApps */
    $compact = $compact ?? false;
@endphp

@if(filled($merchantAccount['account_number'] ?? null))
<div style="{{ $compact ? 'margin-top: 1rem;' : '' }}">
    <p style="margin: 0 0 0.75rem; font-weight: 600; {{ $compact ? 'font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;' : '' }}">Receiving account</p>
    <div style="font-size: {{ $compact ? '0.8rem' : '0.85rem' }}; line-height: 1.5;">
        <strong>{{ $merchantAccount['bank_label'] }}</strong><br>
        @if(filled($merchantAccount['account_name'] ?? null))
            {{ $merchantAccount['account_name'] }}<br>
        @endif
        <span style="font-family: monospace; letter-spacing: 0.03em;">{{ $merchantAccount['account_number'] }}</span>
    </div>
    @if(! empty($paymentApps))
        <p style="margin: 0.75rem 0 0; font-size: {{ $compact ? '0.75rem' : '0.8rem' }}; color: rgba(30,58,42,0.65);">
            Customers may pay via: {{ collect($paymentApps)->pluck('label')->implode(', ') }}
        </p>
    @endif
</div>
@endif
