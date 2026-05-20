<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TaxClassification;
use App\Models\User;
use App\Support\PaymentMethods;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CounterOrderService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly TaxCalculationService $taxCalculation,
    ) {}

    /**
     * @param  array{
     *     first_name: string,
     *     last_name: string,
     *     phone: string,
     *     email?: string|null,
     *     notes?: string|null,
     *     coupon_code?: string|null,
     *     manual_discount?: float|int|string|null,
     *     items: list<array{product_id: int, quantity: int}>,
     *     custom_lines?: list<array{
     *         description: string,
     *         quantity: int,
     *         unit_price_minor: int,
     *         discount_minor?: int,
     *         tax_classification_id?: int|null
     *     }>
     * }  $data
     */
    public function createPending(array $data, User $user): Order
    {
        return $this->createOrder($data, $user);
    }

    /**
     * @param  array{
     *     first_name: string,
     *     last_name: string,
     *     phone: string,
     *     email?: string|null,
     *     notes?: string|null,
     *     coupon_code?: string|null,
     *     manual_discount?: float|int|string|null,
     *     items: list<array{product_id: int, quantity: int}>,
     *     custom_lines?: list<array<string, mixed>>
     * }  $data
     */
    public function createAndComplete(
        array $data,
        User $user,
        string $paymentMethod,
        ?string $paymentReference = null,
        ?string $paymentBank = null,
    ): Order {
        PaymentMethods::validateCounterPayment(
            $paymentMethod,
            $paymentBank,
            $paymentReference,
        );

        $order = $this->createOrder($data, $user);

        $this->recordPaymentAndFulfill($order, $paymentMethod, $paymentReference, $paymentBank);

        return $order->fresh(['items', 'shippingAddress', 'createdBy']);
    }

    public function recordPaymentAndFulfill(
        Order $order,
        string $paymentMethod,
        ?string $paymentReference = null,
        ?string $paymentBank = null,
    ): void {
        PaymentMethods::validateCounterPayment(
            $paymentMethod,
            $paymentBank,
            $paymentReference,
        );
        if (! $order->isCounter()) {
            throw ValidationException::withMessages([
                'payment_reference' => 'Payment recording is only available for counter orders.',
            ]);
        }

        if ($order->payment_status !== PaymentStatus::Pending) {
            throw ValidationException::withMessages([
                'payment_reference' => 'This order payment has already been recorded.',
            ]);
        }

        $this->stockService->assertLineItemsAvailable(
            $order->items
                ->filter(fn (OrderItem $item): bool => $item->product_id !== null)
                ->map(fn (OrderItem $item): array => [
                    'product_id' => (int) $item->product_id,
                    'quantity' => (int) $item->quantity,
                ])
                ->all(),
        );

        DB::transaction(function () use ($order, $paymentMethod, $paymentReference, $paymentBank): void {
            $order->update([
                'payment_status' => PaymentStatus::Paid,
                'payment_reference' => filled($paymentReference) ? $paymentReference : null,
                'status' => OrderStatus::Completed,
                'metadata' => array_merge($order->metadata ?? [], PaymentMethods::metadataPayload(
                    $paymentMethod,
                    $paymentBank,
                )),
            ]);
        });

        $this->incrementCouponUsage($order->metadata['coupon_code'] ?? null);
    }

    public function recordPayment(
        Order $order,
        string $paymentMethod,
        ?string $paymentReference = null,
        ?string $paymentBank = null,
    ): void {
        PaymentMethods::validateCounterPayment(
            $paymentMethod,
            $paymentBank,
            $paymentReference,
        );

        if (! $order->isCounter()) {
            throw ValidationException::withMessages([
                'payment_reference' => 'Payment recording is only available for counter orders.',
            ]);
        }

        if ($order->payment_status !== PaymentStatus::Pending) {
            throw ValidationException::withMessages([
                'payment_reference' => 'This order payment has already been recorded.',
            ]);
        }

        $order->update([
            'payment_status' => PaymentStatus::Paid,
            'payment_reference' => filled($paymentReference) ? $paymentReference : null,
            'metadata' => array_merge($order->metadata ?? [], PaymentMethods::metadataPayload(
                $paymentMethod,
                $paymentBank,
            )),
        ]);

        $this->incrementCouponUsage($order->metadata['coupon_code'] ?? null);
    }

    /**
     * @param  array{
     *     first_name: string,
     *     last_name: string,
     *     phone: string,
     *     email?: string|null,
     *     notes?: string|null,
     *     coupon_code?: string|null,
     *     manual_discount?: float|int|string|null,
     *     items: list<array{product_id: int, quantity: int}>,
     *     custom_lines?: list<array<string, mixed>>
     * }  $data
     */
    private function createOrder(array $data, User $user): Order
    {
        $lineItems = $this->normalizeProductLineItems($data['items'] ?? []);
        $customLines = $this->normalizeCustomLineItems($data['custom_lines'] ?? []);
        $this->assertHasLineItems($lineItems, $customLines);
        $this->stockService->assertLineItemsAvailable($lineItems);

        $pricing = $this->calculateTotals(
            $lineItems,
            $customLines,
            $data['coupon_code'] ?? null,
            $this->manualDiscountMinor($data['manual_discount'] ?? null),
        );

        return DB::transaction(function () use ($data, $user, $lineItems, $customLines, $pricing): Order {
            $address = OrderAddress::query()->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'street_address' => 'In-store counter sale',
                'city' => 'Thimphu',
                'postal_code' => 'N/A',
                'phone' => $data['phone'],
                'country_name' => 'Bhutan',
            ]);

            $order = Order::query()->create([
                'number' => 'OTH-'.strtoupper(substr(uniqid(), -6)),
                'total_minor' => $pricing['total_minor'],
                'currency_code' => 'BTN',
                'status' => OrderStatus::New,
                'payment_status' => PaymentStatus::Pending,
                'notes' => $data['notes'] ?? null,
                'payment_proof_path' => null,
                'payment_reference' => null,
                'payment_access_token' => null,
                'fulfillment_method' => 'counter',
                'metadata' => [
                    'email' => $data['email'] ?? null,
                    'source' => 'counter',
                    'fulfillment_method' => 'counter',
                    'subtotal_minor' => $pricing['subtotal_minor'],
                    'discount_minor' => $pricing['discount_minor'],
                    'coupon_code' => $pricing['coupon_code'],
                    'manual_discount_minor' => $pricing['manual_discount_minor'],
                    'gst_minor' => $pricing['gst_minor'],
                    'effective_tax_rate' => $pricing['effective_tax_rate'],
                    'tax_breakdown' => $pricing['tax_breakdown'],
                ],
                'shipping_address_id' => $address->id,
                'created_by_user_id' => $user->id,
            ]);

            foreach ($lineItems as $lineItem) {
                $product = Product::query()->findOrFail($lineItem['product_id']);

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $lineItem['quantity'],
                    'unit_price_minor' => $product->price_minor,
                    'discount_minor' => 0,
                    'tax_classification_id' => $product->tax_classification_id,
                    'sku' => $product->sku ?? '',
                ]);
            }

            foreach ($customLines as $line) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => null,
                    'name' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price_minor' => $line['unit_price_minor'],
                    'discount_minor' => $line['discount_minor'],
                    'tax_classification_id' => $line['tax_classification_id'],
                    'sku' => null,
                ]);
            }

            return $order->fresh(['items', 'shippingAddress', 'createdBy']);
        });
    }

    /**
     * @param  list<array{product_id?: int|null, quantity?: int|null}>  $items
     * @return list<array{product_id: int, quantity: int}>
     */
    private function normalizeProductLineItems(array $items): array
    {
        return collect($items)
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null))
            ->map(fn (array $item): array => [
                'product_id' => (int) $item['product_id'],
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  list<array<string, mixed>>  $lines
     * @return list<array{
     *     description: string,
     *     quantity: int,
     *     unit_price_minor: int,
     *     discount_minor: int,
     *     tax_classification_id: int|null
     * }>
     */
    private function normalizeCustomLineItems(array $lines): array
    {
        return collect($lines)
            ->filter(fn (array $line): bool => filled($line['description'] ?? null))
            ->map(function (array $line): array {
                $description = trim((string) ($line['description'] ?? ''));

                if ($description === '') {
                    throw ValidationException::withMessages([
                        'custom_lines' => 'Each custom line needs a description.',
                    ]);
                }

                $unitPrice = (int) ($line['unit_price_minor'] ?? 0);

                if ($unitPrice < 1) {
                    throw ValidationException::withMessages([
                        'custom_lines' => "“{$description}” needs a unit price greater than zero.",
                    ]);
                }

                return [
                    'description' => $description,
                    'quantity' => max(1, (int) ($line['quantity'] ?? 1)),
                    'unit_price_minor' => $unitPrice,
                    'discount_minor' => max(0, (int) ($line['discount_minor'] ?? 0)),
                    'tax_classification_id' => filled($line['tax_classification_id'] ?? null)
                        ? (int) $line['tax_classification_id']
                        : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  list<array{product_id: int, quantity: int}>  $productLines
     * @param  list<array{description: string, quantity: int, unit_price_minor: int, discount_minor: int, tax_classification_id: int|null}>  $customLines
     */
    private function assertHasLineItems(array $productLines, array $customLines): void
    {
        if ($productLines === [] && $customLines === []) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one product or custom line to the order.',
            ]);
        }
    }

    /**
     * @param  list<array{product_id: int, quantity: int}>  $productLines
     * @param  list<array{description: string, quantity: int, unit_price_minor: int, discount_minor: int, tax_classification_id: int|null}>  $customLines
     * @return array{
     *     subtotal_minor: int,
     *     discount_minor: int,
     *     manual_discount_minor: int,
     *     coupon_code: ?string,
     *     gst_minor: int,
     *     effective_tax_rate: float,
     *     tax_breakdown: list<array>,
     *     total_minor: int
     * }
     */
    public function calculateTotals(
        array $productLines,
        array $customLines = [],
        ?string $couponCode = null,
        int $manualDiscountMinor = 0,
    ): array {
        $this->assertHasLineItems($productLines, $customLines);

        $lineInputs = $this->buildTaxLineInputs($productLines, $customLines);
        $summary = $this->taxCalculation->summarizeLines($lineInputs);
        $subtotalMinor = $summary['subtotal_minor'];

        $manualDiscountMinor = max(0, min($subtotalMinor, $manualDiscountMinor));
        $couponDiscount = 0;
        $resolvedCouponCode = null;

        if (filled($couponCode)) {
            $coupon = $this->resolveCoupon($couponCode);

            if ($coupon === null || ! $this->couponApplies($coupon)) {
                throw ValidationException::withMessages([
                    'coupon_code' => 'Invalid or expired coupon code.',
                ]);
            }

            $couponDiscount = match ($coupon->type) {
                CouponType::Percent => (int) floor($subtotalMinor * min(100, $coupon->value) / 100),
                CouponType::FixedMinor => (int) min($subtotalMinor, $coupon->value),
            };
            $resolvedCouponCode = $coupon->code;
        }

        $discountMinor = min($subtotalMinor, $manualDiscountMinor + $couponDiscount);

        if ($discountMinor > 0 && $subtotalMinor > 0) {
            $ratio = min(1, $discountMinor / $subtotalMinor);
            $summary['discount_minor'] = $discountMinor;
            $summary['tax_minor'] = (int) round($summary['tax_minor'] * (1 - $ratio));
            $summary['total_minor'] = $summary['subtotal_minor'] - $discountMinor + $summary['tax_minor'];
        }

        $taxableMinor = max(0, $subtotalMinor - $discountMinor);
        $effectiveTaxRate = $taxableMinor > 0
            ? round(($summary['tax_minor'] / $taxableMinor) * 100, 2)
            : 0.0;

        return [
            'subtotal_minor' => $summary['subtotal_minor'],
            'discount_minor' => $discountMinor,
            'manual_discount_minor' => $manualDiscountMinor,
            'coupon_code' => $resolvedCouponCode,
            'gst_minor' => $summary['tax_minor'],
            'effective_tax_rate' => $effectiveTaxRate,
            'tax_breakdown' => $this->buildTaxBreakdownFromSummary($summary['lines'], $discountMinor, $subtotalMinor),
            'total_minor' => $summary['total_minor'],
        ];
    }

    /**
     * @param  list<array{product_id: int, quantity: int}>  $productLines
     * @param  list<array{description: string, quantity: int, unit_price_minor: int, discount_minor: int, tax_classification_id: int|null}>  $customLines
     * @return list<array<string, mixed>>
     */
    private function buildTaxLineInputs(array $productLines, array $customLines): array
    {
        $lineInputs = [];

        foreach ($productLines as $lineItem) {
            $product = Product::query()->findOrFail($lineItem['product_id']);

            $lineInputs[] = [
                'unit_price_minor' => $product->price_minor,
                'quantity' => $lineItem['quantity'],
                'discount_minor' => 0,
                'product' => $product,
            ];
        }

        foreach ($customLines as $line) {
            $classification = $line['tax_classification_id'] !== null
                ? TaxClassification::query()->find($line['tax_classification_id'])
                : null;

            $lineInputs[] = [
                'unit_price_minor' => $line['unit_price_minor'],
                'quantity' => $line['quantity'],
                'discount_minor' => $line['discount_minor'],
                'tax_classification' => $classification,
            ];
        }

        return $lineInputs;
    }

    /**
     * @param  list<array>  $computedLines
     * @return list<array{code: string, name: string, rate_percent: float, tax_minor: int}>
     */
    private function buildTaxBreakdownFromSummary(array $computedLines, int $discountMinor, int $subtotalMinor): array
    {
        $ratio = ($discountMinor > 0 && $subtotalMinor > 0)
            ? min(1, $discountMinor / $subtotalMinor)
            : 0;

        $byCode = [];

        foreach ($computedLines as $line) {
            $classification = $line['tax_classification'] ?? ($line['product']?->taxClassification);
            $code = $classification?->code ?? 'UNKNOWN';
            $taxMinor = (int) round(((int) ($line['tax_minor'] ?? 0)) * (1 - $ratio));

            if ($taxMinor <= 0) {
                continue;
            }

            if (! isset($byCode[$code])) {
                $byCode[$code] = [
                    'code' => $code,
                    'name' => $classification?->name ?? $code,
                    'rate_percent' => (float) ($line['tax_rate_percent'] ?? 0),
                    'tax_minor' => 0,
                ];
            }

            $byCode[$code]['tax_minor'] += $taxMinor;
        }

        return array_values($byCode);
    }

    private function manualDiscountMinor(mixed $manualDiscount): int
    {
        if ($manualDiscount === null || $manualDiscount === '') {
            return 0;
        }

        $amount = (float) $manualDiscount;

        if ($amount <= 0) {
            return 0;
        }

        return (int) round($amount * 100);
    }

    private function resolveCoupon(?string $code): ?Coupon
    {
        if (! filled($code)) {
            return null;
        }

        return Coupon::query()
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])
            ->first();
    }

    private function couponApplies(Coupon $coupon): bool
    {
        if (! $coupon->is_active) {
            return false;
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return false;
        }

        if ($coupon->ends_at && $coupon->ends_at->isPast()) {
            return false;
        }

        if ($coupon->max_uses !== null && $coupon->uses_count >= $coupon->max_uses) {
            return false;
        }

        return true;
    }

    private function incrementCouponUsage(?string $couponCode): void
    {
        $coupon = $this->resolveCoupon($couponCode);

        if ($coupon === null || ! $this->couponApplies($coupon)) {
            return;
        }

        $coupon->increment('uses_count');
    }
}
