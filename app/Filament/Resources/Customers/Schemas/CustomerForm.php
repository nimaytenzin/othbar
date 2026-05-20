<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    /**
     * Fields for inline customer creation (e.g. from invoice issue form).
     *
     * @return list<TextInput|Textarea|Toggle>
     */
    public static function createOptionFields(): array
    {
        return [
            TextInput::make('display_name')
                ->label('Name')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('phone')->tel()->maxLength(50),
            TextInput::make('gst_tpn')->label('GST TPN')->maxLength(50),
            TextInput::make('billing_address_line1')->label('Address line 1'),
            TextInput::make('billing_city')->label('City'),
            Toggle::make('is_active')->default(true),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextInput::make('display_name')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(50),
                TextInput::make('gst_tpn')->label('GST TPN')->maxLength(50),
                TextInput::make('billing_address_line1')->label('Address line 1'),
                TextInput::make('billing_address_line2')->label('Address line 2'),
                TextInput::make('billing_city')->label('City'),
                TextInput::make('billing_district')->label('District'),
                TextInput::make('billing_postal_code')->label('Postal code'),
                Toggle::make('is_active')->default(true),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }
}
