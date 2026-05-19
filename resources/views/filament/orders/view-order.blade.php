@php
    /** @var \App\Models\Order $order */
    use App\Support\PaymentChannels;

    $order->loadMissing(['items', 'shippingAddress']);
    $meta = is_array($order->metadata) ? $order->metadata : (json_decode((string) $order->metadata, true) ?: []);
    $customerEmail = $meta['email'] ?? null;
    $couponCode = $meta['coupon_code'] ?? null;
    $isPickup = $order->isPickup();
    $isCounter = $order->isCounter();
    $receiptUrl = route('filament.admin.orders.receipt', $order).'?autoprint=1';
    $pricing = $order->pricingSummary();
    $merchantAccount = PaymentChannels::merchantAccount();
    $paymentApps = PaymentChannels::paymentApps();
    $fulfillmentSteps = $order->fulfillmentSteps();
    $canMarkFulfilled = $order->canMarkFulfilled();
    $canCancel = ! $order->isCompleted() && ! $order->isCancelled();
    $cancelDialogId = 'oth-cancel-dialog-'.$order->id;
@endphp

<div class="oth-order-page">
    <div class="oth-order-main">
        <div class="oth-card">
            <div class="oth-order-head">
                <div>
                    <p class="oth-card__subtitle" style="margin:0;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Order</p>
                    <h2 class="oth-order-number">{{ $order->number }}</h2>
                    <p class="oth-order-meta">Placed {{ $order->created_at?->format('M j, Y g:i A') }}</p>
                </div>
                @if($order->isCancelled())
                    <x-filament::badge color="danger" icon="heroicon-o-x-circle">
                        Cancelled
                    </x-filament::badge>
                @elseif($order->isCompleted())
                    <x-filament::badge color="success" icon="heroicon-o-check-badge">
                        Fulfilled
                    </x-filament::badge>
                @endif
            </div>

            <div class="oth-progress" style="margin-top: 1.25rem;">
                @foreach($fulfillmentSteps as $index => $step)
                    <div class="oth-progress__step oth-progress__step--{{ $step['state'] }}">
                        <div class="oth-progress__marker">
                            @if($step['state'] === 'complete')
                                <span aria-hidden="true">✓</span>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div class="oth-progress__content">
                            <p class="oth-progress__label">{{ $step['label'] }}</p>
                            <p class="oth-progress__description">{{ $step['description'] }}</p>
                        </div>
                    </div>
                    @if(! $loop->last)
                        <div class="oth-progress__connector oth-progress__connector--{{ $step['state'] === 'complete' ? 'complete' : 'upcoming' }}"></div>
                    @endif
                @endforeach
            </div>

            @if($canMarkFulfilled)
                @php $fulfillDialogId = 'oth-fulfill-dialog-'.$order->id; @endphp
                <div style="margin-top: 1.25rem;">
                    <button type="button"
                        onclick="document.getElementById(@js($fulfillDialogId)).showModal()"
                        class="oth-btn oth-btn--success">
                        {{ $isCounter ? 'Mark counter sale fulfilled' : ($isPickup ? 'Mark pickup fulfilled' : 'Mark delivery fulfilled') }}
                    </button>
                </div>

                <x-oth-confirm-dialog
                    :id="$fulfillDialogId"
                    :title="$isCounter ? 'Confirm counter sale' : ($isPickup ? 'Confirm in-store pickup' : 'Confirm delivery')"
                    :message="$isCounter
                        ? 'Has the customer received this in-store order? This will mark the order as fulfilled.'
                        : ($isPickup
                        ? 'Has the customer collected this order in store? This will mark the order as fulfilled.'
                        : 'Has this order been delivered to the customer? This will mark the order as fulfilled.')"
                    :confirm-label="$isCounter ? 'Mark counter sale fulfilled' : ($isPickup ? 'Mark pickup fulfilled' : 'Mark delivery fulfilled')"
                    confirm-variant="success"
                    confirm-target="markFulfilled"
                    confirm-loading-target="markFulfilled"
                />
            @endif

            <dl class="oth-stat-grid" style="margin-top: 1.25rem;">
                <div class="oth-stat">
                    <dt class="oth-stat__label">Total</dt>
                    <dd class="oth-stat__value">Nu. {{ number_format($order->total_minor / 100) }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">Fulfillment</dt>
                    <dd class="oth-stat__value">{{ $isCounter ? 'Counter sale' : ($isPickup ? 'In-store pickup' : 'Delivery') }}</dd>
                </div>
                <div class="oth-stat">
                    <dt class="oth-stat__label">Currency</dt>
                    <dd class="oth-stat__value">{{ $order->currency_code }}</dd>
                </div>
            </dl>
        </div>

        <div class="oth-card">
            <h3 class="oth-card__title">{{ $isCounter ? 'Customer' : 'Customer & delivery' }}</h3>
            @if($order->shippingAddress)
            <dl class="oth-dl">
                <div>
                    <dt>Name</dt>
                    <dd>{{ $order->shippingAddress->full_name }}</dd>
                </div>
                <div>
                    <dt>Phone</dt>
                    <dd><a href="tel:{{ $order->shippingAddress->phone }}">{{ $order->shippingAddress->phone }}</a></dd>
                </div>
                @if($customerEmail)
                <div class="oth-dl__full">
                    <dt>Email</dt>
                    <dd><a href="mailto:{{ $customerEmail }}">{{ $customerEmail }}</a></dd>
                </div>
                @endif
                @if(! $isCounter)
                <div class="oth-dl__full">
                    <dt>{{ $isPickup ? 'Pickup' : 'Address' }}</dt>
                    <dd>
                        @if($isPickup)
                            {{ $order->shippingAddress->street_address }}
                        @else
                            {{ $order->shippingAddress->street_address }}<br>
                            {{ $order->shippingAddress->city }} {{ $order->shippingAddress->postal_code }}
                        @endif
                    </dd>
                </div>
                @endif
                @if($couponCode)
                <div>
                    <dt>Coupon</dt>
                    <dd><code>{{ $couponCode }}</code></dd>
                </div>
                @endif
            </dl>
            @else
            <p class="oth-order-meta" style="margin-top:1rem;">No customer address on file.</p>
            @endif
        </div>

        <div class="oth-card oth-card--flush">
            <div class="oth-card__header">
                <h3 class="oth-card__title">Line items</h3>
            </div>
            <div class="oth-table-wrap">
                <table class="oth-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit</th>
                            <th class="text-right">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td><strong>{{ $item->name }}</strong></td>
                            <td style="font-family:monospace;font-size:0.75rem;color:#6b7280;">{{ $item->sku ?: '—' }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">Nu. {{ number_format($item->unit_price_minor / 100) }}</td>
                            <td class="text-right"><strong>Nu. {{ number_format($item->line_total_minor / 100) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Subtotal</td>
                            <td class="text-right">Nu. {{ number_format($pricing['subtotal_minor'] / 100) }}</td>
                        </tr>
                        @if($pricing['discount_minor'] > 0)
                        <tr>
                            <td colspan="4" class="text-right">Discount</td>
                            <td class="text-right" style="color:#b45309;">− Nu. {{ number_format($pricing['discount_minor'] / 100) }}</td>
                        </tr>
                        @endif
                        @if($pricing['gst_minor'] > 0)
                        <tr>
                            <td colspan="4" class="text-right">GST ({{ rtrim(rtrim(number_format($pricing['gst_percentage'], 2), '0'), '.') }}%)</td>
                            <td class="text-right">Nu. {{ number_format($pricing['gst_minor'] / 100) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="text-right"><strong>Order total</strong></td>
                            <td class="text-right" style="font-size:1.125rem;"><strong>Nu. {{ number_format($order->total_minor / 100) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if(filled($order->notes))
        <div class="oth-card">
            <h3 class="oth-card__title">Customer notes</h3>
            <p style="margin:0.75rem 0 0;font-size:0.875rem;line-height:1.6;white-space:pre-wrap;">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    <div class="oth-order-side">
        <div class="oth-card">
            <h3 class="oth-card__title">Quick actions</h3>
            <div class="oth-actions">
                <a href="{{ $receiptUrl }}" target="_blank" rel="noopener" class="oth-btn oth-btn--primary">
                    Print receipt
                </a>
                @if($order->payment_access_token)
                <a href="{{ route('checkout.pay', ['order' => $order->id, 'token' => $order->payment_access_token]) }}" target="_blank" rel="noopener" class="oth-btn oth-btn--secondary">
                    Customer pay page
                </a>
                @endif
                @if($canCancel)
                <button type="button"
                    onclick="document.getElementById(@js($cancelDialogId)).showModal()"
                    class="oth-btn oth-btn--danger">
                    Cancel order
                </button>
                @endif
            </div>

            @if($canCancel)
            <x-oth-confirm-dialog
                :id="$cancelDialogId"
                title="Cancel order"
                message="Are you sure you want to cancel this order? Pending payments will be voided. This action cannot be undone."
                confirm-label="Yes, cancel order"
                cancel-label="Keep order"
                confirm-variant="danger"
                confirm-target="cancelOrder"
                confirm-loading-target="cancelOrder"
            />
            @endif
        </div>

        @if(! $isCounter)
        <div class="oth-card">
            <h3 class="oth-card__title">Bank details</h3>
            <p class="oth-card__subtitle" style="margin-top:0.25rem;">Edit in Admin → Payment &amp; GST</p>
            <div style="margin-top:1rem;">
                @include('partials.order-bank-details', [
                    'merchantAccount' => $merchantAccount,
                    'paymentApps' => $paymentApps,
                    'compact' => true,
                ])
            </div>
        </div>
        @endif

        <div class="oth-card oth-card--flush">
            <div class="oth-card__header">
                <h3 class="oth-card__title">{{ $isCounter ? 'Counter payment' : 'Payment & proof' }}</h3>
                <p class="oth-card__subtitle">{{ $isCounter ? 'Cash or bank transfer via '.\App\Support\PaymentMethods::paymentAppNames() : 'Verify bank transfer and uploaded screenshot' }}</p>
            </div>
            <div class="oth-card__body" style="padding-top:0;">
                @if($isCounter)
                    @livewire(\App\Livewire\Admin\CounterPaymentRecording::class, ['order' => $order], key('cp-'.$order->id))
                @else
                    @livewire(\App\Livewire\Admin\PaymentVerification::class, ['order' => $order], key('pv-'.$order->id))
                @endif
            </div>
        </div>
    </div>
</div>
