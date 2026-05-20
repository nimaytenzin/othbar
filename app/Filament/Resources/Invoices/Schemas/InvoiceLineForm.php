<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TaxClassification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class InvoiceLineForm
{
    public static function defaultTaxClassificationId(): ?int
    {
        $settings = SiteSetting::current();

        if ($settings->default_tax_classification_id !== null) {
            return (int) $settings->default_tax_classification_id;
        }

        $standard = TaxClassification::query()->where('code', 'STANDARD')->value('id');

        return $standard !== null ? (int) $standard : null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultFormState(): array
    {
        return [
            'quantity' => 1,
            'discount_minor' => 0,
            'tax_classification_id' => self::defaultTaxClassificationId(),
        ];
    }

    /**
     * @return list<Select|TextInput>
     */
    public static function fields(): array
    {
        return [
            Select::make('product_id')
                ->label('Product (optional)')
                ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    if ($state) {
                        $product = Product::query()->find($state);
                        if ($product) {
                            $set('description', $product->name);
                            $set('unit_price_minor', $product->price_minor / 100);
                            $set(
                                'tax_classification_id',
                                $product->tax_classification_id ?? self::defaultTaxClassificationId(),
                            );
                        }
                    } else {
                        $set('tax_classification_id', self::defaultTaxClassificationId());
                    }
                }),
            TextInput::make('description')
                ->label('Description')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->required(),
            TextInput::make('unit_price_minor')
                ->label('Unit price (Nu.)')
                ->numeric()
                ->required()
                ->minValue(0.01)
                ->formatStateUsing(function ($state) {
                    if ($state === null || $state === '') {
                        return null;
                    }

                    // Stored line data uses minor units; form input uses Nu.
                    return (int) $state >= 100
                        ? ((int) $state) / 100
                        : $state;
                })
                ->dehydrateStateUsing(fn ($state) => (int) round((float) $state * 100)),
            TextInput::make('discount_minor')
                ->label('Line discount (Nu.)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->formatStateUsing(function ($state) {
                    if ($state === null || $state === '') {
                        return null;
                    }

                    return (int) $state >= 100
                        ? ((int) $state) / 100
                        : $state;
                })
                ->dehydrateStateUsing(fn ($state) => (int) round((float) ($state ?? 0) * 100)),
            Select::make('tax_classification_id')
                ->label('Tax rate')
                ->options(fn () => TaxClassification::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get()
                    ->mapWithKeys(fn (TaxClassification $t) => [
                        $t->id => "{$t->name} ({$t->rate_percent}%)",
                    ]))
                ->default(fn () => self::defaultTaxClassificationId())
                ->required()
                ->searchable(),
        ];
    }
}
