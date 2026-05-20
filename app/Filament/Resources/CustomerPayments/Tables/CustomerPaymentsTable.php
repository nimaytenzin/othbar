<?php

namespace App\Filament\Resources\CustomerPayments\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                TextColumn::make('number')->searchable()->sortable(),
                TextColumn::make('customer.display_name')->label('Customer'),
                TextColumn::make('payment_date')->date(),
                TextColumn::make('amount_minor')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => 'Nu. '.number_format(((int) $state) / 100, 2)),
                TextColumn::make('payment_method')->label('Method'),
            ])
            ->recordActions([
                Action::make('receipt')
                    ->label('Receipt')
                    ->url(fn ($record) => route('filament.admin.payments.receipt', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
