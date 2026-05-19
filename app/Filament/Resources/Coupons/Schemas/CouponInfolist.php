<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponType;
use App\Models\Coupon;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),
                TextEntry::make('type')->badge(),
                TextEntry::make('value')
                    ->label('Discount')
                    ->formatStateUsing(function ($state, Coupon $record): string {
                        if ($record->type === CouponType::FixedMinor) {
                            return 'Nu. '.number_format(((int) $state) / 100);
                        }

                        return ((int) $state).'%';
                    }),
                TextEntry::make('starts_at')->dateTime(),
                TextEntry::make('ends_at')->dateTime(),
                TextEntry::make('max_uses'),
                TextEntry::make('uses_count'),
                IconEntry::make('is_active')->boolean(),
            ]);
    }
}
