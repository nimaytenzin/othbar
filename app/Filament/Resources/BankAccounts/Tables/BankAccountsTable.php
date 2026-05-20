<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->searchable(),
                TextColumn::make('bank_name')->searchable(),
                TextColumn::make('account_name'),
                TextColumn::make('account_number'),
                IconColumn::make('is_default')->boolean()->label('Default'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
