<?php

namespace App\Filament\Resources\CustomerPayments;

use App\Filament\Resources\CustomerPayments\Pages\ListCustomerPayments;
use App\Filament\Resources\CustomerPayments\Tables\CustomerPaymentsTable;
use App\Models\CustomerPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CustomerPaymentResource extends Resource
{
    protected static ?string $model = CustomerPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;

    protected static ?string $navigationLabel = 'Payment receipts';

    protected static string|UnitEnum|null $navigationGroup = 'Payments';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->can('payments.receive');
    }

    public static function table(Table $table): Table
    {
        return CustomerPaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomerPayments::route('/'),
        ];
    }
}
