<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Enums\CouponType;
use App\Models\Coupon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('value')
                    ->label('Discount')
                    ->formatStateUsing(function ($state, Coupon $record): string {
                        if ($record->type === CouponType::FixedMinor) {
                            return 'Nu. '.number_format(((int) $state) / 100);
                        }

                        return ((int) $state).'%';
                    })
                    ->sortable(),
                TextColumn::make('uses_count')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
