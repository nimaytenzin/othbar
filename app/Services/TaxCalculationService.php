<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TaxClassification;

class TaxCalculationService
{
    /**
     * @return array{
     *     tax_classification_id: int|null,
     *     tax_rate_percent: float,
     *     line_subtotal_minor: int,
     *     tax_minor: int,
     *     line_total_minor: int
     * }
     */
    public function calculateLine(
        int $unitPriceMinor,
        int $quantity,
        int $discountMinor = 0,
        ?TaxClassification $classification = null,
        ?Product $product = null,
        ?bool $isGstRegistered = null,
    ): array {
        $classification ??= $this->resolveClassification($product);
        $lineSubtotal = max(0, ($unitPriceMinor * $quantity) - $discountMinor);
        $isGstRegistered ??= (bool) SiteSetting::current()->is_gst_registered;

        if ($classification === null || $classification->isExempt() || ! $isGstRegistered) {
            return [
                'tax_classification_id' => $classification?->id,
                'tax_rate_percent' => 0,
                'line_subtotal_minor' => $lineSubtotal,
                'tax_minor' => 0,
                'line_total_minor' => $lineSubtotal,
            ];
        }

        $rate = (float) $classification->rate_percent;
        $taxMinor = (int) round($lineSubtotal * ($rate / 100));

        return [
            'tax_classification_id' => $classification->id,
            'tax_rate_percent' => $rate,
            'line_subtotal_minor' => $lineSubtotal,
            'tax_minor' => $taxMinor,
            'line_total_minor' => $lineSubtotal + $taxMinor,
        ];
    }

    public function resolveClassification(?Product $product = null): ?TaxClassification
    {
        if ($product?->tax_classification_id !== null) {
            return $product->taxClassification;
        }

        $settings = SiteSetting::current();

        if ($settings->default_tax_classification_id !== null) {
            return $settings->defaultTaxClassification;
        }

        return TaxClassification::query()->where('code', 'STANDARD')->first();
    }

    /**
     * @param  list<array{unit_price_minor: int, quantity: int, discount_minor?: int, product?: Product|null, tax_classification?: TaxClassification|null}>  $lines
     * @return array{subtotal_minor: int, discount_minor: int, tax_minor: int, total_minor: int, lines: list<array>}
     */
    public function summarizeLines(array $lines): array
    {
        $subtotal = 0;
        $tax = 0;
        $discount = 0;
        $computed = [];

        foreach ($lines as $line) {
            $discountMinor = (int) ($line['discount_minor'] ?? 0);
            $calc = $this->calculateLine(
                (int) $line['unit_price_minor'],
                (int) $line['quantity'],
                $discountMinor,
                $line['tax_classification'] ?? null,
                $line['product'] ?? null,
            );

            $discount += $discountMinor;
            $subtotal += $calc['line_subtotal_minor'];
            $tax += $calc['tax_minor'];
            $computed[] = array_merge($line, $calc);
        }

        return [
            'subtotal_minor' => $subtotal,
            'discount_minor' => $discount,
            'tax_minor' => $tax,
            'total_minor' => $subtotal + $tax,
            'lines' => $computed,
        ];
    }

    /**
     * @param  list<array{product_id: int, quantity: int, unit_price_minor?: int}>  $lineItems
     * @return array{
     *     subtotal_minor: int,
     *     discount_minor: int,
     *     tax_minor: int,
     *     gst_minor: int,
     *     total_minor: int,
     *     effective_tax_rate: float,
     *     tax_breakdown: list<array{code: string, name: string, rate_percent: float, tax_minor: int}>,
     *     lines: list<array>
     * }
     */
    public function calculateCartTotals(
        array $lineItems,
        int $discountMinor = 0,
        ?bool $isGstRegistered = null,
    ): array {
        if ($lineItems === []) {
            return [
                'subtotal_minor' => 0,
                'discount_minor' => 0,
                'tax_minor' => 0,
                'gst_minor' => 0,
                'total_minor' => 0,
                'effective_tax_rate' => 0,
                'tax_breakdown' => [],
                'lines' => [],
            ];
        }

        $isGstRegistered ??= (bool) SiteSetting::current()->is_gst_registered;
        $productIds = collect($lineItems)->pluck('product_id')->unique()->all();
        $products = Product::query()
            ->with('taxClassification')
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $lineInputs = [];
        foreach ($lineItems as $lineItem) {
            $product = $products->get($lineItem['product_id']);
            if ($product === null) {
                continue;
            }

            $lineInputs[] = [
                'product_id' => $product->id,
                'unit_price_minor' => (int) ($lineItem['unit_price_minor'] ?? $product->price_minor),
                'quantity' => (int) $lineItem['quantity'],
                'product' => $product,
            ];
        }

        $summary = $this->summarizeLines($lineInputs);
        $subtotalMinor = $summary['subtotal_minor'];
        $discountMinor = min($subtotalMinor, max(0, $discountMinor));
        $taxMinor = $summary['tax_minor'];

        if ($discountMinor > 0 && $subtotalMinor > 0 && $taxMinor > 0) {
            $ratio = min(1, $discountMinor / $subtotalMinor);
            $taxMinor = (int) round($taxMinor * (1 - $ratio));
        }

        $taxableMinor = max(0, $subtotalMinor - $discountMinor);
        $effectiveTaxRate = $taxableMinor > 0
            ? round(($taxMinor / $taxableMinor) * 100, 2)
            : 0.0;

        return [
            'subtotal_minor' => $subtotalMinor,
            'discount_minor' => $discountMinor,
            'tax_minor' => $taxMinor,
            'gst_minor' => $taxMinor,
            'total_minor' => $taxableMinor + $taxMinor,
            'effective_tax_rate' => $effectiveTaxRate,
            'tax_breakdown' => $this->buildTaxBreakdown($summary['lines'], $discountMinor, $subtotalMinor),
            'lines' => $summary['lines'],
        ];
    }

    /**
     * @param  list<array>  $computedLines
     * @return list<array{code: string, name: string, rate_percent: float, tax_minor: int}>
     */
    private function buildTaxBreakdown(array $computedLines, int $discountMinor, int $subtotalMinor): array
    {
        $ratio = ($discountMinor > 0 && $subtotalMinor > 0)
            ? min(1, $discountMinor / $subtotalMinor)
            : 0;

        $byCode = [];
        foreach ($computedLines as $line) {
            $product = $line['product'] ?? null;
            $classification = $line['tax_classification'] ?? ($product?->taxClassification);
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
}
