@php
    $subtotal = $subtotalMinor ?? 0;
    $discount = $discountMinor ?? 0;
    $gst = $gstMinor ?? 0;
    $effectiveRate = $effectiveTaxRate ?? ($gstPercentage ?? 0);
    $showTaxRate = $showTaxRate ?? ($effectiveRate > 0);
    $total = $totalMinor ?? 0;
    $rowStyle = $rowStyle ?? 'display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; font-size: 0.88rem; color: #1E3A2A;';
    $totalRowStyle = $totalRowStyle ?? 'display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-top: 2px solid #1E3A2A; font-weight: 600;';
    $totalAmountStyle = $totalAmountStyle ?? "font-family: 'Cormorant Garamond', serif; font-size: 1.75rem; font-weight: 700; color: #1E3A2A;";
@endphp

<div style="{{ $rowStyle }} border-top: 1px solid #D8CCAD; padding-top: 0.75rem;">
    <span>Subtotal</span>
    <span>Nu. {{ number_format($subtotal / 100) }}</span>
</div>
@if($discount > 0)
<div style="{{ $rowStyle }}">
    <span>Discount</span>
    <span style="color: #C4843C;">− Nu. {{ number_format($discount / 100) }}</span>
</div>
@endif
@if($gst > 0)
<div style="{{ $rowStyle }}">
    <span>
        @if($showTaxRate && $effectiveRate > 0)
            GST ({{ rtrim(rtrim(number_format($effectiveRate, 2), '0'), '.') }}%)
        @else
            GST
        @endif
    </span>
    <span>Nu. {{ number_format($gst / 100) }}</span>
</div>
@endif
<div style="{{ $totalRowStyle }}">
    <span style="font-size: 1rem;">Total</span>
    <span style="{{ $totalAmountStyle }}">Nu. {{ number_format($total / 100) }}</span>
</div>
