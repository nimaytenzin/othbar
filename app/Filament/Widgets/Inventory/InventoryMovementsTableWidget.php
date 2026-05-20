<?php

namespace App\Filament\Widgets\Inventory;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class InventoryMovementsTableWidget extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent inventory movements')
            ->description('Stock changes from sales, restocks, and adjustments.')
            ->query(
                InventoryMovement::query()->with(['product', 'user']),
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (InventoryMovementType $state): string => $state->label())
                    ->color(fn (InventoryMovementType $state): string => $state->color()),
                TextColumn::make('quantity_delta')
                    ->label('Change')
                    ->alignEnd()
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "+{$state}" : (string) $state)
                    ->color(fn (int $state): string => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray')),
                TextColumn::make('quantity_after')
                    ->label('On hand after')
                    ->alignEnd()
                    ->numeric(),
                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System')
                    ->toggleable(),
                TextColumn::make('notes')
                    ->limit(40)
                    ->tooltip(fn (?string $state): ?string => $state)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(collect(InventoryMovementType::cases())->mapWithKeys(
                        fn (InventoryMovementType $type) => [$type->value => $type->label()],
                    )),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No inventory movements yet')
            ->emptyStateDescription('Movements are recorded when tracked products are sold or restocked through completed orders.')
            ->emptyStateIcon(Heroicon::OutlinedArrowsRightLeft)
            ->paginated([10, 25, 50]);
    }

    public static function canView(): bool
    {
        return Auth::user()?->can('inventory.view') ?? false;
    }
}
