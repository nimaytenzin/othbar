<?php

namespace App\Filament\Resources\TaxClassifications\Tables;

use App\Models\InvoiceItem;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxClassificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rate_percent')
                    ->label('Rate')
                    ->suffix('%')
                    ->sortable(),
                IconColumn::make('input_credits_claimable')
                    ->label('Input credits')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record): bool => ! $record->isSystemCode())
                    ->before(function (DeleteAction $action, $record): void {
                        $inUse = Product::query()->where('tax_classification_id', $record->id)->exists()
                            || InvoiceItem::query()->where('tax_classification_id', $record->id)->exists();

                        if ($inUse) {
                            Notification::make()
                                ->title('Cannot delete classification')
                                ->body('This classification is in use by products or invoices.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    }),
            ]);
    }
}
