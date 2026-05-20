<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'number',
        'total_minor',
        'currency_code',
        'status',
        'payment_status',
        'shipping_status',
        'notes',
        'payment_proof_path',
        'payment_reference',
        'payment_access_token',
        'fulfillment_method',
        'metadata',
        'shipping_address_id',
        'created_by_user_id',
        'customer_id',
        'invoice_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'shipping_status' => ShippingStatus::class,
            'metadata' => 'array',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(OrderAddress::class, 'shipping_address_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isCounter(): bool
    {
        return ($this->fulfillment_method ?? '') === 'counter'
            || (($this->metadata['source'] ?? null) === 'counter');
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid;
    }

    public function isPickup(): bool
    {
        return ($this->fulfillment_method ?? 'delivery') === 'pickup';
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeCounter(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->where('fulfillment_method', 'counter')
                ->orWhere('metadata->source', 'counter');
        });
    }

    public function canMarkFulfilled(): bool
    {
        return $this->isPaid()
            && ! in_array($this->status, [OrderStatus::Completed, OrderStatus::Cancelled], true);
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::Cancelled;
    }

    /**
     * @return list<array{key: string, label: string, description: string, state: 'complete'|'current'|'upcoming'|'cancelled'}>
     */
    public function fulfillmentSteps(): array
    {
        if ($this->isCounter()) {
            $fulfilledLabel = 'Counter sale fulfilled';
            $fulfilledDescription = 'Customer received items in store';
        } else {
            $fulfilledLabel = $this->isPickup() ? 'Pickup fulfilled' : 'Delivery fulfilled';
            $fulfilledDescription = $this->isPickup()
                ? 'Customer collected the order in store'
                : 'Order delivered to the customer';
        }

        $steps = [
            [
                'key' => 'pending',
                'label' => 'Pending',
                'description' => $this->isCounter()
                    ? 'Payment not recorded'
                    : 'Payment not confirmed',
            ],
            [
                'key' => 'paid',
                'label' => 'Payment confirmed',
                'description' => $this->isCounter()
                    ? 'Journal entry or transaction recorded'
                    : 'Bank transfer verified',
            ],
            [
                'key' => 'fulfilled',
                'label' => $fulfilledLabel,
                'description' => $fulfilledDescription,
            ],
        ];

        if ($this->isCancelled()) {
            return array_map(
                fn (array $step): array => [...$step, 'state' => 'cancelled'],
                $steps,
            );
        }

        $activeIndex = match (true) {
            $this->isCompleted() => 2,
            $this->isPaid() => 1,
            default => 0,
        };

        return array_map(
            function (array $step, int $index) use ($activeIndex): array {
                if ($index < $activeIndex) {
                    return [...$step, 'state' => 'complete'];
                }

                if ($index === $activeIndex) {
                    return [...$step, 'state' => 'current'];
                }

                return [...$step, 'state' => 'upcoming'];
            },
            $steps,
            array_keys($steps),
        );
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query
            ->where('payment_status', PaymentStatus::Paid)
            ->where('status', OrderStatus::Completed);
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeCreatedBetween(Builder $query, string $from, string $to): Builder
    {
        return $query->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
    }

    /**
     * Active orders still in the pipeline (cumulative — not scoped to a single day).
     *
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNotIn('status', [OrderStatus::Completed, OrderStatus::Cancelled]);
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeCompletedOnDay(Builder $query, ?string $day = null): Builder
    {
        return $query
            ->where('status', OrderStatus::Completed)
            ->whereDate('updated_at', $day ?? today()->toDateString());
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public function scopeCancelledOnDay(Builder $query, ?string $day = null): Builder
    {
        return $query
            ->where('status', OrderStatus::Cancelled)
            ->whereDate('updated_at', $day ?? today()->toDateString());
    }

    /**
     * @return array{
     *     subtotal_minor: int,
     *     discount_minor: int,
     *     gst_minor: int,
     *     effective_tax_rate: float,
     *     gst_percentage: float,
     *     total_minor: int,
     *     has_gst_breakdown: bool,
     *     tax_breakdown: list<array>|null,
     *     show_tax_rate: bool
     * }
     */
    public function pricingSummary(): array
    {
        $meta = $this->metadata ?? [];
        $itemsSubtotal = (int) $this->items->sum(fn (OrderItem $item) => $item->line_total_minor);

        if (array_key_exists('gst_minor', $meta)) {
            $subtotal = (int) ($meta['subtotal_minor'] ?? $itemsSubtotal);
            $discount = (int) ($meta['discount_minor'] ?? 0);
            $gstMinor = (int) $meta['gst_minor'];
            $taxable = max(0, $subtotal - $discount);

            $effectiveRate = (float) ($meta['effective_tax_rate']
                ?? $meta['gst_percentage']
                ?? ($taxable > 0 ? round(($gstMinor / $taxable) * 100, 2) : 0));

            $taxBreakdown = $meta['tax_breakdown'] ?? null;
            $showTaxRate = is_array($taxBreakdown)
                ? count($taxBreakdown) <= 1
                : true;

            return [
                'subtotal_minor' => $subtotal,
                'discount_minor' => $discount,
                'gst_minor' => $gstMinor,
                'effective_tax_rate' => $effectiveRate,
                'gst_percentage' => $effectiveRate,
                'total_minor' => (int) $this->total_minor,
                'has_gst_breakdown' => true,
                'tax_breakdown' => $taxBreakdown,
                'show_tax_rate' => $showTaxRate,
            ];
        }

        return [
            'subtotal_minor' => $itemsSubtotal,
            'discount_minor' => max(0, $itemsSubtotal - (int) $this->total_minor),
            'gst_minor' => 0,
            'effective_tax_rate' => 0,
            'gst_percentage' => 0,
            'total_minor' => (int) $this->total_minor,
            'has_gst_breakdown' => false,
            'tax_breakdown' => null,
            'show_tax_rate' => true,
        ];
    }
}
