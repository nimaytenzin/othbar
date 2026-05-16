<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->enum(\App\Enums\CouponType::class)
                    ->required(),
                TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->helperText('Percent (0–100) or fixed amount in minor units (e.g. chetrum).'),
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
