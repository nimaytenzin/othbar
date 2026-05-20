<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'quantity',
        'unit_price_minor',
        'discount_minor',
        'tax_classification_id',
        'sku',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function taxClassification(): BelongsTo
    {
        return $this->belongsTo(TaxClassification::class);
    }

    public function getLineTotalMinorAttribute(): int
    {
        return $this->unit_price_minor * $this->quantity;
    }
}
