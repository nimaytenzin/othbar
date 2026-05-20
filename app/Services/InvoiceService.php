<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TaxClassification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function __construct(
        private readonly DocumentNumberService $documentNumbers,
        private readonly TaxCalculationService $taxCalculation,
        private readonly CustomerService $customers,
    ) {}

    public function createFromOrder(Order $order, ?User $user = null): Invoice
    {
        $existing = Invoice::query()->where('order_id', $order->id)->first();

        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($order, $user): Invoice {
            $order->loadMissing(['items.product', 'shippingAddress']);
            $settings = SiteSetting::current();
            $customer = $this->customers->findOrCreateFromOrder($order);

            $issueDate = $order->created_at?->toDateString() ?? now()->toDateString();
            $dueDate = now()->parse($issueDate)->addDays($settings->invoice_payment_terms_days ?? 30)->toDateString();

            $order->loadMissing(['items.product.taxClassification', 'items.taxClassification']);

            $lineInputs = $order->items->map(function (OrderItem $item): array {
                return [
                    'unit_price_minor' => (int) $item->unit_price_minor,
                    'quantity' => (int) $item->quantity,
                    'discount_minor' => (int) ($item->discount_minor ?? 0),
                    'product' => $item->product,
                    'tax_classification' => $item->product?->taxClassification ?? $item->taxClassification,
                ];
            })->all();

            $summary = $this->taxCalculation->summarizeLines($lineInputs);

            $meta = $order->metadata ?? [];
            $orderDiscount = (int) ($meta['discount_minor'] ?? 0);

            if ($orderDiscount > 0 && $summary['subtotal_minor'] > 0) {
                $ratio = min(1, $orderDiscount / $summary['subtotal_minor']);
                $summary['discount_minor'] = $orderDiscount;
                $summary['tax_minor'] = (int) round($summary['tax_minor'] * (1 - $ratio));
                $summary['total_minor'] = $summary['subtotal_minor'] - $orderDiscount + $summary['tax_minor'];
            }

            $invoice = Invoice::query()->create([
                'number' => $this->documentNumbers->next(DocumentType::Invoice, $order->created_at),
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'status' => $order->payment_status === PaymentStatus::Paid
                    ? InvoiceStatus::Paid
                    : InvoiceStatus::Sent,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'subtotal_minor' => $summary['subtotal_minor'],
                'discount_minor' => $summary['discount_minor'],
                'tax_minor' => $summary['tax_minor'],
                'total_minor' => $summary['total_minor'],
                'amount_paid_minor' => $order->payment_status === PaymentStatus::Paid
                    ? $summary['total_minor']
                    : 0,
                'currency_code' => $order->currency_code,
                'notes' => $order->notes,
                'terms_snapshot' => $settings->invoice_terms_text,
                'created_by_user_id' => $user?->id ?? $order->created_by_user_id,
            ]);

            foreach ($order->items as $index => $item) {
                $calc = $summary['lines'][$index] ?? $this->taxCalculation->calculateLine(
                    (int) $item->unit_price_minor,
                    (int) $item->quantity,
                    (int) ($item->discount_minor ?? 0),
                    $item->product?->taxClassification ?? $item->taxClassification,
                    $item->product,
                );

                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'description' => $item->name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'unit_price_minor' => $item->unit_price_minor,
                    'discount_minor' => (int) ($item->discount_minor ?? 0),
                    'tax_classification_id' => $calc['tax_classification_id'],
                    'tax_rate_percent' => $calc['tax_rate_percent'],
                    'tax_minor' => $calc['tax_minor'],
                    'line_total_minor' => $calc['line_total_minor'],
                ]);
            }

            $order->update(['invoice_id' => $invoice->id]);

            return $invoice->fresh(['items', 'customer', 'order']);
        });
    }

    /**
     * @param  list<array{product_id?: int|null, description: string, quantity: int, unit_price_minor: int, discount_minor?: int, tax_classification_id?: int|null}>  $lines
     */
    public function createManual(
        int $customerId,
        array $lines,
        ?User $user = null,
        ?string $notes = null,
        ?string $issueDate = null,
        ?string $dueDate = null,
        int $invoiceDiscountMinor = 0,
    ): Invoice {
        return DB::transaction(function () use ($customerId, $lines, $user, $notes, $issueDate, $dueDate, $invoiceDiscountMinor): Invoice {
            $settings = SiteSetting::current();
            $lineInputs = [];

            foreach ($lines as $line) {
                $product = isset($line['product_id'])
                    ? Product::query()->find($line['product_id'])
                    : null;

                $classification = isset($line['tax_classification_id'])
                    ? TaxClassification::query()->find($line['tax_classification_id'])
                    : null;

                $lineInputs[] = [
                    'unit_price_minor' => (int) $line['unit_price_minor'],
                    'quantity' => (int) $line['quantity'],
                    'discount_minor' => (int) ($line['discount_minor'] ?? 0),
                    'product' => $product,
                    'tax_classification' => $classification,
                ];
            }

            $summary = $this->taxCalculation->summarizeLines($lineInputs);

            if ($invoiceDiscountMinor > 0 && $summary['subtotal_minor'] > 0) {
                $ratio = min(1, $invoiceDiscountMinor / $summary['subtotal_minor']);
                $summary['discount_minor'] += $invoiceDiscountMinor;
                $summary['tax_minor'] = (int) round($summary['tax_minor'] * (1 - $ratio));
                $summary['total_minor'] = $summary['subtotal_minor'] - $summary['discount_minor'] + $summary['tax_minor'];
            }

            $issue = $issueDate ?? now()->toDateString();
            $due = $dueDate ?? now()->parse($issue)->addDays($settings->invoice_payment_terms_days ?? 30)->toDateString();

            $invoice = Invoice::query()->create([
                'number' => $this->documentNumbers->next(DocumentType::Invoice),
                'customer_id' => $customerId,
                'status' => InvoiceStatus::Sent,
                'issue_date' => $issue,
                'due_date' => $due,
                'subtotal_minor' => $summary['subtotal_minor'],
                'discount_minor' => $summary['discount_minor'],
                'tax_minor' => $summary['tax_minor'],
                'total_minor' => $summary['total_minor'],
                'currency_code' => $settings->default_currency ?? 'BTN',
                'notes' => $notes,
                'terms_snapshot' => $settings->invoice_terms_text,
                'created_by_user_id' => $user?->id,
            ]);

            foreach ($lines as $index => $line) {
                $calc = $summary['lines'][$index];
                $product = isset($line['product_id'])
                    ? Product::query()->find($line['product_id'])
                    : null;

                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product?->id,
                    'description' => $line['description'],
                    'sku' => $product?->sku,
                    'quantity' => (int) $line['quantity'],
                    'unit_price_minor' => (int) $line['unit_price_minor'],
                    'discount_minor' => (int) ($line['discount_minor'] ?? 0),
                    'tax_classification_id' => $calc['tax_classification_id'],
                    'tax_rate_percent' => $calc['tax_rate_percent'],
                    'tax_minor' => $calc['tax_minor'],
                    'line_total_minor' => $calc['line_total_minor'],
                ]);
            }

            return $invoice->fresh(['items', 'customer']);
        });
    }

    public function void(Invoice $invoice): Invoice
    {
        if (! $invoice->canVoid()) {
            throw ValidationException::withMessages([
                'invoice' => $invoice->voidBlockReason()
                    ?? 'This invoice cannot be voided.',
            ]);
        }

        $invoice->update(['status' => InvoiceStatus::Void]);

        return $invoice->fresh();
    }
}
