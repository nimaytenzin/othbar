<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
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
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('payment_status')->badge()->sortable(),
                TextColumn::make('shipping_status')->badge()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fulfillment_method')->badge(),
                TextColumn::make('total_minor')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => 'Nu. '.number_format(((int) $state) / 100))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
