<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'sku',
        'quantity',
        'unit_price_minor',
        'discount_minor',
        'tax_classification_id',
        'tax_rate_percent',
        'tax_minor',
        'line_total_minor',
    ];

    protected function casts(): array
    {
        return [
            'tax_rate_percent' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxClassification(): BelongsTo
    {
        return $this->belongsTo(TaxClassification::class);
    }

    public function lineSubtotalMinor(): int
    {
        return max(0, ((int) $this->unit_price_minor * (int) $this->quantity) - (int) $this->discount_minor);
    }
}
