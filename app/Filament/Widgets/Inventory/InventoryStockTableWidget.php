<?php

namespace App\Filament\Widgets\Inventory;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InventoryStockTableWidget extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Stock levels')
            ->description('Products with inventory tracking enabled. Update tracking on each product under Catalog.')
            ->query(
                Product::query()
                    ->where('track_inventory', true)
                    ->with('brand'),
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): ?string => $record->sku)
                    ->weight('medium'),
                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('stock_quantity')
                    ->label('On hand')
                    ->sortable()
                    ->alignEnd()
                    ->numeric()
                    ->badge()
                    ->color(fn (Product $record): string => static::stockColor($record)),
                TextColumn::make('reorder_level')
                    ->label('Reorder at')
                    ->alignEnd()
                    ->placeholder('5 units')
                    ->toggleable(),
                TextColumn::make('stock_status')
                    ->label('Status')
                    ->badge()
                    ->state(fn (Product $record): string => static::stockStatusLabel($record))
                    ->color(fn (Product $record): string => static::stockColor($record)),
            ])
            ->filters([
                SelectFilter::make('stock_status')
                    ->label('Status')
                    ->options([
                        'low' => 'Low stock',
                        'out' => 'Out of stock',
                        'ok' => 'In stock',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'out' => $query->where('stock_quantity', '<=', 0),
                            'low' => $query->where('stock_quantity', '>', 0)->where(function ($q): void {
                                $q->where(function ($q2): void {
                                    $q2->whereNotNull('reorder_level')
                                        ->whereColumn('stock_quantity', '<=', 'reorder_level');
                                })->orWhere(function ($q2): void {
                                    $q2->whereNull('reorder_level')->where('stock_quantity', '<=', 5);
                                });
                            }),
                            'ok' => $query->where('stock_quantity', '>', 0)->where(function ($q): void {
                                $q->where(function ($q2): void {
                                    $q2->whereNotNull('reorder_level')
                                        ->whereColumn('stock_quantity', '>', 'reorder_level');
                                })->orWhere(function ($q2): void {
                                    $q2->whereNull('reorder_level')->where('stock_quantity', '>', 5);
                                });
                            }),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('stock_quantity', 'asc')
            ->recordActions([
                ViewAction::make()->url(fn (Product $record): string => ProductResource::getUrl('view', ['record' => $record])),
                EditAction::make()->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('No products with inventory tracking')
            ->emptyStateDescription('Enable "Track inventory" on a product to monitor stock levels and movements here.')
            ->emptyStateIcon(Heroicon::OutlinedArchiveBox)
            ->emptyStateActions([
                Action::make('browse_products')
                    ->label('Manage products')
                    ->icon(Heroicon::OutlinedCube)
                    ->url(ProductResource::getUrl('index')),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function stockStatusLabel(Product $product): string
    {
        if ($product->stock_quantity <= 0) {
            return 'Out of stock';
        }

        $reorder = $product->reorder_level ?? 5;

        if ($product->stock_quantity <= $reorder) {
            return 'Low stock';
        }

        return 'In stock';
    }

    public static function stockColor(Product $product): string
    {
        if ($product->stock_quantity <= 0) {
            return 'danger';
        }

        $reorder = $product->reorder_level ?? 5;

        return $product->stock_quantity <= $reorder ? 'warning' : 'success';
    }

    public static function canView(): bool
    {
        return Auth::user()?->can('inventory.view') ?? false;
    }
}
