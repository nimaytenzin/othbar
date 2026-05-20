@php
    /** @var array{bank_label: string, account_name: string, account_number: string, qr_url: ?string} $merchantAccount */
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
</div>
@endif
