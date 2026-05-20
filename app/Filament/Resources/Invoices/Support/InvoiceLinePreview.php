<?php

namespace App\Filament\Resources\Invoices\Support;

use App\Filament\Resources\Invoices\Schemas\InvoiceLineForm;
use App\Models\Product;
use App\Models\TaxClassification;
use App\Services\TaxCalculationService;

class InvoiceLinePreview
{
    /**
     * @param  list<array<string, mixed>>  $lines
     * @return array{
     *     rows: list<array{
     *         description: string,
     *         quantity: int,
     *         unit_price_minor: int,
     *         discount_minor: int,
     *         tax_rate_percent: float,
     *         tax_minor: int,
     *         line_total_minor: int,
     *         product_name: ?string
     *     }>,
     *     subtotal_minor: int,
     *     discount_minor: int,
     *     tax_minor: int,
     *     total_minor: int
     * }
     */
    public static function summarize(array $lines, int $invoiceDiscountMinor = 0): array
    {
        $tax = app(TaxCalculationService::class);
        $lineInputs = [];

        foreach ($lines as $line) {
            $product = filled($line['product_id'] ?? null)
                ? Product::query()->find($line['product_id'])
                : null;

            $classification = filled($line['tax_classification_id'] ?? null)
                ? TaxClassification::query()->find($line['tax_classification_id'])
                : null;

            $lineInputs[] = [
                'unit_price_minor' => (int) ($line['unit_price_minor'] ?? 0),
                'quantity' => (int) ($line['quantity'] ?? 1),
                'discount_minor' => (int) ($line['discount_minor'] ?? 0),
                'product' => $product,
                'tax_classification' => $classification,
            ];
        }

        $summary = $tax->summarizeLines($lineInputs);

        if ($invoiceDiscountMinor > 0 && $summary['subtotal_minor'] > 0) {
            $ratio = min(1, $invoiceDiscountMinor / $summary['subtotal_minor']);
            $summary['discount_minor'] += $invoiceDiscountMinor;
            $summary['tax_minor'] = (int) round($summary['tax_minor'] * (1 - $ratio));
            $summary['total_minor'] = $summary['subtotal_minor'] - $summary['discount_minor'] + $summary['tax_minor'];
        }

        $rows = [];

        foreach ($lines as $index => $line) {
            $calc = $summary['lines'][$index] ?? [];
            $product = filled($line['product_id'] ?? null)
                ? Product::query()->find($line['product_id'])
                : null;

            $rows[] = [
                'description' => (string) ($line['description'] ?? ''),
                'quantity' => (int) ($line['quantity'] ?? 1),
                'unit_price_minor' => (int) ($line['unit_price_minor'] ?? 0),
                'discount_minor' => (int) ($line['discount_minor'] ?? 0),
                'tax_rate_percent' => (float) ($calc['tax_rate_percent'] ?? 0),
                'tax_minor' => (int) ($calc['tax_minor'] ?? 0),
                'line_total_minor' => (int) ($calc['line_total_minor'] ?? 0),
                'product_name' => $product?->name,
            ];
        }

        return [
            'rows' => $rows,
            'subtotal_minor' => $summary['subtotal_minor'],
            'discount_minor' => $summary['discount_minor'],
            'tax_minor' => $summary['tax_minor'],
            'total_minor' => $summary['total_minor'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFromForm(array $data): array
    {
        $taxClassificationId = filled($data['tax_classification_id'] ?? null)
            ? (int) $data['tax_classification_id']
            : InvoiceLineForm::defaultTaxClassificationId();

        return [
            'product_id' => filled($data['product_id'] ?? null) ? (int) $data['product_id'] : null,
            'description' => (string) ($data['description'] ?? ''),
            'quantity' => (int) ($data['quantity'] ?? 1),
            'unit_price_minor' => (int) ($data['unit_price_minor'] ?? 0),
            'discount_minor' => (int) ($data['discount_minor'] ?? 0),
            'tax_classification_id' => $taxClassificationId,
        ];
    }
}
