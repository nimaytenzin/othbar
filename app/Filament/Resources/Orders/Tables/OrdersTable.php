<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Filament\Resources\Orders\Pages\ListOrders;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OrdersTable
{
    public static function configure(Table $table, ?ListOrders $livewire = null): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('metadata.source')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'counter' => 'Counter',
                        'storefront' => 'Online',
                        default => $state ? ucfirst($state) : 'Online',
                    })
                    ->color(fn (?string $state): string => $state === 'counter' ? 'info' : 'gray'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->getLabel())
                    ->color(fn (OrderStatus $state): string => $state->getColor())
                    ->icon(fn (OrderStatus $state): ?string => $state->getIcon())
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn (PaymentStatus $state): string => $state->getLabel())
                    ->color(fn (PaymentStatus $state): string => $state->getColor())
                    ->icon(fn (PaymentStatus $state): ?string => $state->getIcon())
                    ->sortable(),
                TextColumn::make('shipping_status')
                    ->label('Shipping')
                    ->badge()
                    ->formatStateUsing(fn (ShippingStatus $state): string => $state->getLabel())
                    ->color(fn (ShippingStatus $state): string => $state->getColor())
                    ->icon(fn (ShippingStatus $state): ?string => $state->getIcon())
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fulfillment_method')
                    ->label('Fulfillment')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                        'counter' => 'Counter',
                        default => $state ? ucfirst($state) : '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pickup' => 'warning',
                        'counter' => 'info',
                        'delivery' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('total_minor')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => 'Nu. '.number_format(((int) $state) / 100))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(fn (): string => $livewire?->activeTab === 'completed'
                        ? 'Completed'
                        : ($livewire?->activeTab === 'cancelled' ? 'Cancelled' : 'Updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort(
                fn (): string => in_array($livewire?->activeTab, ['completed', 'cancelled'], true) ? 'updated_at' : 'created_at',
                'desc',
            )
            ->filters(static::filters(), layout: FiltersLayout::AboveContent)
            ->filtersFormColumns([
                'default' => 1,
                'sm' => 2,
                'lg' => 3,
            ])
            ->deferFilters(false)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    /**
     * @return array<int, Filter|SelectFilter>
     */
    protected static function filters(): array
    {
        return [
            Filter::make('order_day')
                ->label('Date')
                ->columnSpan(1)
                ->schema([
                    DatePicker::make('date')
                        ->label('Orders for')
                        ->default(today())
                        ->maxDate(today())
                        ->native(false)
                        ->closeOnDateSelection()
                        ->live()
                        ->disabled(fn (ListOrders $livewire): bool => ! $livewire->tabUsesDayFilter())
                        ->helperText(fn (ListOrders $livewire): string => $livewire->tabUsesDayFilter()
                            ? 'Completed and cancelled orders on this day'
                            : 'Switch to the Completed or Cancelled tab to filter by day'),
                ])
                ->default(['date' => today()->toDateString()])
                ->query(fn (Builder $query): Builder => $query)
                ->indicateUsing(function (array $data, ListOrders $livewire): ?Indicator {
                    if (! $livewire->tabUsesDayFilter() || ! filled($data['date'] ?? null)) {
                        return null;
                    }

                    return Indicator::make('Day: '.Carbon::parse($data['date'])->format('M j, Y'))
                        ->removeField('date');
                }),
            SelectFilter::make('source')
                ->label('Source')
                ->options([
                    'storefront' => 'Online',
                    'counter' => 'Counter',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $value = $data['value'] ?? null;

                    if ($value === 'counter') {
                        return $query->where(function (Builder $query): void {
                            $query->where('fulfillment_method', 'counter')
                                ->orWhere('metadata->source', 'counter');
                        });
                    }

                    if ($value === 'storefront') {
                        return $query->where(function (Builder $query): void {
                            $query->where(function (Builder $query): void {
                                $query->whereNull('metadata->source')
                                    ->orWhere('metadata->source', 'storefront');
                            })->where(function (Builder $query): void {
                                $query->whereNull('fulfillment_method')
                                    ->orWhere('fulfillment_method', '!=', 'counter');
                            });
                        });
                    }

                    return $query;
                }),
        ];
    }
}
