<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->disabled(),
                Select::make('status')
                    ->enum(OrderStatus::class)
                    ->required(),
                Select::make('payment_status')
                    ->enum(PaymentStatus::class)
                    ->required(),
                Select::make('shipping_status')
                    ->enum(ShippingStatus::class)
                    ->required(),
                TextInput::make('payment_reference')->maxLength(255)->nullable(),
                Textarea::make('notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
