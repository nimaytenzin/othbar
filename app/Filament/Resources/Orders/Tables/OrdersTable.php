<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
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
                //
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
