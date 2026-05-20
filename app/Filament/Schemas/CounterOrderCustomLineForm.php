<?php

namespace App\Filament\Schemas;

use App\Filament\Resources\Invoices\Schemas\InvoiceLineForm;
use App\Models\TaxClassification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class CounterOrderCustomLineForm
{
    /**
     * @return array<string, mixed>
     */
    public static function defaultFormState(): array
    {
        return InvoiceLineForm::defaultFormState();
    }

    /**
     * @return list<Select|TextInput>
     */
    public static function fields(): array
    {
        return [
            TextInput::make('description')
                ->label('Description')
                ->placeholder('e.g. Packaging fee, delivery charge')
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
                ->default(fn () => InvoiceLineForm::defaultTaxClassificationId())
                ->required()
                ->searchable(),
        ];
    }
}
