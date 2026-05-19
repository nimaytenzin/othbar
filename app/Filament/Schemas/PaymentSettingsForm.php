<?php

namespace App\Filament\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(static::components());
    }

    /**
     * @return array<int, Component>
     */
    public static function components(): array
    {
        return [
            Section::make('GST / tax')
                ->description('GST is calculated on the order amount after coupon discounts (subtotal minus discount).')
                ->schema([
                    TextInput::make('gst_percentage')
                        ->label('GST rate')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%')
                        ->default(5)
                        ->helperText('Set to 0 to disable GST on new orders.')
                        ->required(),
                ]),
            Section::make('Receiving bank account')
                ->description('One account where customers transfer payment. On checkout they choose which mobile app (mBoB, mPAY, ePay, etc.) they used — those apps are fixed in the system.')
                ->schema([
                    TextInput::make('payment_merchant_account.bank_label')
                        ->label('Bank name')
                        ->helperText('e.g. Bank of Bhutan (BoB)')
                        ->maxLength(150)
                        ->required(),
                    TextInput::make('payment_merchant_account.account_name')
                        ->label('Account name')
                        ->maxLength(150)
                        ->required(),
                    TextInput::make('payment_merchant_account.account_number')
                        ->label('Account number')
                        ->maxLength(80)
                        ->required(),
                    FileUpload::make('payment_merchant_account.qr_path')
                        ->label('Payment QR code')
                        ->disk('public')
                        ->directory('payment-qr')
                        ->image()
                        ->maxSize(2048)
                        ->helperText('Scan-to-pay QR for this account (works with supported mobile banking apps).')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
