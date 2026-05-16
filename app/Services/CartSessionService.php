<?php

namespace App\Services;

use App\Exceptions\InvalidCouponException;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartSessionService
{
    public const SESSION_KEY = 'othbar_cart';

    /**
     * @return array{lines: list<array{product_id: int, quantity: int, unit_price_amount: int}>, coupon_code: ?string}
     */
    public function raw(): array
    {
        return Session::get(self::SESSION_KEY, [
            'lines' => [],
            'coupon_code' => null,
        ]);
    }

    /**
     * @param  array{lines: list<array{product_id: int, quantity: int, unit_price_amount: int}>, coupon_code: ?string}  $data
     */
    public function save(array $data): void
    {
        Session::put(self::SESSION_KEY, [
            'lines' => array_values($data['lines']),
            'coupon_code' => $data['coupon_code'] ?? null,
        ]);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /** @return list<array{product_id: int, quantity: int, unit_price_amount: int}> */
    public function lineRows(): array
    {
        return $this->raw()['lines'];
    }

    public function couponCode(): ?string
    {
        return $this->raw()['coupon_code'];
    }

    /** Meta object for Blade ($cart->coupon_code, $cart->currency_code). */
    public function cartViewModel(): object
    {
        $raw = $this->raw();

        return (object) [
            'coupon_code' => $raw['coupon_code'],
            'currency_code' => 'BTN',
        ];
    }

    public function subtotalMinor(): int
    {
        $sum = 0;
        foreach ($this->lineRows() as $line) {
            $sum += $line['unit_price_amount'] * $line['quantity'];
        }

        return $sum;
    }

    public function resolvedCoupon(): ?Coupon
    {
        $code = $this->couponCode();
        if (! $code) {
            return null;
        }

        return Coupon::query()
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])
            ->first();
    }

    public function discountMinor(): int
    {
        $coupon = $this->resolvedCoupon();
        if (! $coupon || ! $this->couponApplies($coupon)) {
            return 0;
        }

        $subtotal = $this->subtotalMinor();

        return match ($coupon->type) {
            \App\Enums\CouponType::Percent => (int) floor($subtotal * min(100, $coupon->value) / 100),
            \App\Enums\CouponType::FixedMinor => (int) min($subtotal, $coupon->value),
        };
    }

    public function totalMinor(): int
    {
        return max(0, $this->subtotalMinor() - $this->discountMinor());
    }

    public function couponApplies(Coupon $coupon): bool
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

    /**
     * @throws InvalidCouponException
     */
    public function applyCouponCode(string $code): void
    {
        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])
            ->first();

        if (! $coupon) {
            throw new InvalidCouponException(__('Invalid or expired coupon code.'));
        }

        if (! $this->couponApplies($coupon)) {
            throw new InvalidCouponException(__('Invalid or expired coupon code.'));
        }

        $raw = $this->raw();
        $raw['coupon_code'] = $coupon->code;
        $this->save($raw);
    }

    public function removeCoupon(): void
    {
        $raw = $this->raw();
        $raw['coupon_code'] = null;
        $this->save($raw);
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $lines = $this->lineRows();
        $found = false;
        foreach ($lines as &$line) {
            if ((int) $line['product_id'] === (int) $product->id) {
                $line['quantity'] = (int) $line['quantity'] + $quantity;
                $line['unit_price_amount'] = (int) $product->price_minor;
                $found = true;
                break;
            }
        }
        unset($line);

        if (! $found) {
            $lines[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_amount' => (int) $product->price_minor,
            ];
        }

        $raw = $this->raw();
        $this->save(['lines' => $lines, 'coupon_code' => $raw['coupon_code']]);
    }

    public function updateLineQuantity(int $index, int $quantity): void
    {
        $lines = $this->lineRows();
        if (! isset($lines[$index])) {
            return;
        }
        $lines[$index]['quantity'] = max(1, $quantity);
        $raw = $this->raw();
        $this->save(['lines' => $lines, 'coupon_code' => $raw['coupon_code']]);
    }

    public function removeLine(int $index): void
    {
        $lines = $this->lineRows();
        if (! isset($lines[$index])) {
            return;
        }
        unset($lines[$index]);
        $raw = $this->raw();
        $this->save(['lines' => array_values($lines), 'coupon_code' => $raw['coupon_code']]);
    }

    /**
     * Eager-loaded products keyed by line index for storefront views.
     *
     * @return Collection<int, object{purchasable: Product, quantity: int, unit_price_amount: int}>
     */
    public function linesWithProducts(): Collection
    {
        $rows = $this->lineRows();
        if ($rows === []) {
            return collect();
        }

        $ids = collect($rows)->pluck('product_id')->unique()->all();
        $products = Product::query()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($rows)->map(function (array $line, int $index) use ($products) {
            $product = $products[$line['product_id']] ?? null;

            return (object) [
                'line_index' => $index,
                'purchasable' => $product,
                'quantity' => (int) $line['quantity'],
                'unit_price_amount' => (int) $line['unit_price_amount'],
            ];
        })->values();
    }
}
