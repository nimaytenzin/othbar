<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()->columns(2)->schema([
                TextInput::make('label')->maxLength(150),
                TextInput::make('bank_name')->required()->maxLength(150),
                TextInput::make('account_name')->required()->maxLength(150),
                TextInput::make('account_number')->required()->maxLength(80),
                TextInput::make('branch')->maxLength(150),
                TextInput::make('swift_or_code')->maxLength(50),
                Toggle::make('is_default')->label('Default on invoices'),
                Toggle::make('is_active')->default(true),
                FileUpload::make('qr_path')
                    ->label('Payment QR')
                    ->disk('public')
                    ->directory('payment-qr')
                    ->image()
                    ->columnSpanFull(),
                Textarea::make('notes')->columnSpanFull(),
            ]),
        ]);
    }
}
