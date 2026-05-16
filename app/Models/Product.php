<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'summary',
        'description',
        'sku',
        'stock_quantity',
        'allow_backorder',
        'is_visible',
        'price_minor',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'allow_backorder' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    protected function prices(): Attribute
    {
        return Attribute::get(fn () => collect([(object) ['amount' => $this->price_minor]]));
    }

    protected function stock(): Attribute
    {
        return Attribute::get(fn (): int => $this->stock_quantity);
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('uploads');
        $this->addMediaCollection('thumbnail');
    }

    public function inStock(int $quantity): bool
    {
        if ($this->allow_backorder) {
            return true;
        }

        return $this->stock_quantity >= $quantity;
    }
}
