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
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\PaymentMethods;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CounterOrderService
{
    public function __construct(
        private readonly StockService $stockService,
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
     *     items: list<array{product_id: int, quantity: int}>
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
     *     items: list<array{product_id: int, quantity: int}>
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
            $order->items->map(fn (OrderItem $item): array => [
                'product_id' => (int) $item->product_id,
                'quantity' => (int) $item->quantity,
            ])->all(),
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
     *     items: list<array{product_id: int, quantity: int}>
     * }  $data
     */
    private function createOrder(array $data, User $user): Order
    {
        $lineItems = $this->normalizeLineItems($data['items'] ?? []);
        $this->stockService->assertLineItemsAvailable($lineItems);

        $pricing = $this->calculateTotals(
            $lineItems,
            $data['coupon_code'] ?? null,
            $this->manualDiscountMinor($data['manual_discount'] ?? null),
        );

        return DB::transaction(function () use ($data, $user, $lineItems, $pricing): Order {
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
                    'gst_percentage' => $pricing['gst_percentage'],
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
                    'sku' => $product->sku ?? '',
                ]);
            }

            return $order->fresh(['items', 'shippingAddress', 'createdBy']);
        });
    }

    /**
     * @param  list<array{product_id?: int|null, quantity?: int|null}>  $items
     * @return list<array{product_id: int, quantity: int}>
     */
    private function normalizeLineItems(array $items): array
    {
        $lineItems = collect($items)
            ->filter(fn (array $item): bool => filled($item['product_id'] ?? null))
            ->map(fn (array $item): array => [
                'product_id' => (int) $item['product_id'],
                'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
            ])
            ->values()
            ->all();

        if ($lineItems === []) {
            throw ValidationException::withMessages([
                'items' => 'Add at least one product to the order.',
            ]);
        }

        return $lineItems;
    }

    /**
     * @param  list<array{product_id: int, quantity: int}>  $lineItems
     * @return array{
     *     subtotal_minor: int,
     *     discount_minor: int,
     *     manual_discount_minor: int,
     *     coupon_code: ?string,
     *     gst_minor: int,
     *     gst_percentage: float,
     *     total_minor: int
     * }
     */
    public function calculateTotals(
        array $lineItems,
        ?string $couponCode = null,
        int $manualDiscountMinor = 0,
    ): array {
        $subtotalMinor = 0;

        foreach ($lineItems as $lineItem) {
            $product = Product::query()->findOrFail($lineItem['product_id']);
            $subtotalMinor += $product->price_minor * $lineItem['quantity'];
        }

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
        $taxableMinor = max(0, $subtotalMinor - $discountMinor);

        $gstPercentage = max(0, min(100, (float) (SiteSetting::current()->gst_percentage ?? 0)));
        $gstMinor = $gstPercentage > 0
            ? (int) floor($taxableMinor * $gstPercentage / 100)
            : 0;

        return [
            'subtotal_minor' => $subtotalMinor,
            'discount_minor' => $discountMinor,
            'manual_discount_minor' => $manualDiscountMinor,
            'coupon_code' => $resolvedCouponCode,
            'gst_minor' => $gstMinor,
            'gst_percentage' => $gstPercentage,
            'total_minor' => $taxableMinor + $gstMinor,
        ];
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
