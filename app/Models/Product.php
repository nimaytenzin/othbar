<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

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
        'track_inventory',
        'tax_classification_id',
        'reorder_level',
    ];

    protected function casts(): array
    {
        return [
            'allow_backorder' => 'boolean',
            'is_visible' => 'boolean',
            'track_inventory' => 'boolean',
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

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxClassification(): BelongsTo
    {
        return $this->belongsTo(TaxClassification::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
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
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('uploads')
            ->useDisk('public');
    }

    public function featuredImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('thumbnail') ?: $this->getFirstMediaUrl('uploads');

        if ($url === '') {
            return null;
        }

        return str_starts_with($url, 'http') ? $url : url($url);
    }

    /**
     * @return list<string>
     */
    public function galleryImageUrls(): array
    {
        $urls = collect();

        $thumbnail = $this->getFirstMedia('thumbnail');
        if ($thumbnail) {
            $urls->push($thumbnail->getUrl());
        }

        foreach ($this->getMedia('uploads') as $media) {
            $urls->push($media->getUrl());
        }

        return $urls->unique()->values()->all();
    }

    public function inStock(int $quantity): bool
    {
        if ($this->allow_backorder) {
            return true;
        }

        return $this->stock_quantity >= $quantity;
    }
}
