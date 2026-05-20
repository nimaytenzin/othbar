<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxClassification extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'rate_percent',
        'input_credits_claimable',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rate_percent' => 'decimal:2',
            'input_credits_claimable' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function isExempt(): bool
    {
        return $this->code === 'EXEMPT';
    }

    public function isTaxable(): bool
    {
        return ! $this->isExempt();
    }

    public function isSystemCode(): bool
    {
        return in_array($this->code, ['STANDARD', 'ZERO_RATED', 'EXEMPT'], true);
    }

    public static function systemCodes(): array
    {
        return ['STANDARD', 'ZERO_RATED', 'EXEMPT'];
    }

    protected static function booted(): void
    {
        static::deleting(function (TaxClassification $classification): void {
            if ($classification->isSystemCode()) {
                throw new \RuntimeException('System tax classifications cannot be deleted.');
            }
        });
    }
}
