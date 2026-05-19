<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CouponForm
{
    protected static function isFixedNu(Get $get): bool
    {
        $type = $get('type');

        if ($type instanceof CouponType) {
            return $type === CouponType::FixedMinor;
        }

        return (string) $type === CouponType::FixedMinor->value;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->options(CouponType::options())
                    ->required()
                    ->live(),
                TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->label(fn (Get $get): string => static::isFixedNu($get)
                        ? 'Discount amount (Nu.)'
                        : 'Discount (%)')
                    ->suffix(fn (Get $get): ?string => static::isFixedNu($get) ? 'Nu.' : '%')
                    ->helperText(fn (Get $get): string => static::isFixedNu($get)
                        ? 'Fixed discount in Ngultrum. Example: 50 = Nu. 50 off the order.'
                        : ($get('type')
                            ? 'Percentage off the order subtotal (0–100).'
                            : 'Select coupon type: percent or fixed Ngultrum (Nu.) amount.'))
                    ->formatStateUsing(function ($state, Get $get) {
                        if (static::isFixedNu($get) && $state !== null && $state !== '') {
                            return ((int) $state) / 100;
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state, Get $get) {
                        if (static::isFixedNu($get)) {
                            return (int) round((float) $state * 100);
                        }

                        return (int) $state;
                    }),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                TextInput::make('max_uses')
                    ->numeric()
                    ->minValue(1)
                    ->nullable(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
