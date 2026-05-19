@php
    /** @var list<\App\Models\Order> $orders */
    /** @var int $orderCount */
    /** @var int $totalMinor */
    /** @var string $dateFrom */
    /** @var string $dateTo */
@endphp

<div class="oth-card" style="margin-top: 1.5rem;">
    <h3 class="oth-card__title">Successful orders</h3>
    <p class="oth-card__subtitle">
        Paid and fulfilled orders from {{ \Illuminate\Support\Carbon::parse($dateFrom)->format('M j, Y') }}
        to {{ \Illuminate\Support\Carbon::parse($dateTo)->format('M j, Y') }}
    </p>

    <dl class="oth-stat-grid" style="margin-top: 1rem;">
        <div class="oth-stat">
            <dt class="oth-stat__label">Orders</dt>
            <dd class="oth-stat__value">{{ number_format($orderCount) }}</dd>
        </div>
        <div class="oth-stat">
            <dt class="oth-stat__label">Total revenue</dt>
            <dd class="oth-stat__value">Nu. {{ number_format($totalMinor / 100, 2) }}</dd>
        </div>
    </dl>

    @if($orders->isEmpty())
        <p class="oth-order-meta" style="margin-top: 1rem;">No successful orders in this date range.</p>
    @else
        <div class="oth-table-wrap" style="margin-top: 1rem;">
            <table class="oth-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Fulfillment</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('view', ['record' => $order]) }}"
                                   style="font-weight: 600; text-decoration: underline;">
                                    {{ $order->number }}
                                </a>
                            </td>
                            <td>{{ $order->created_at?->format('M j, Y g:i A') }}</td>
                            <td>{{ $order->shippingAddress?->full_name ?: '—' }}</td>
                            <td>{{ $order->isPickup() ? 'Pickup' : 'Delivery' }}</td>
                            <td class="text-right"><strong>Nu. {{ number_format($order->total_minor / 100, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                        <td class="text-right"><strong>Nu. {{ number_format($totalMinor / 100, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</div>
