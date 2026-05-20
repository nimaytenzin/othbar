<?php

namespace App\Filament\Schemas;

use App\Filament\Resources\BankAccounts\BankAccountResource;
use App\Support\PaymentChannels;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

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
            Section::make('Receiving bank account')
                ->description('Used on storefront checkout and payment instructions. Manage accounts under Settings → Bank Accounts.')
                ->schema([
                    Placeholder::make('default_bank_account')
                        ->label('Default account')
                        ->content(fn (): HtmlString => new HtmlString(static::defaultBankAccountHtml())),
                ]),
            Section::make('Pickup')
                ->schema([
                    TextInput::make('pickup_address_label')
                        ->label('Pickup address label')
                        ->maxLength(255),
                ]),
        ];
    }

    protected static function defaultBankAccountHtml(): string
    {
        $account = PaymentChannels::defaultBankAccount();
        $manageUrl = BankAccountResource::getUrl('index');

        if ($account === null) {
            return "No active bank account configured. <a href=\"{$manageUrl}\" class=\"underline font-medium\">Add a bank account</a> and mark it as default.";
        }

        $label = e($account->displayLabel());
        $name = e($account->account_name);
        $number = e($account->account_number);

        return "{$label}<br><span class=\"text-gray-600 dark:text-gray-400\">{$name} · {$number}</span><br><a href=\"{$manageUrl}\" class=\"underline font-medium\">Manage bank accounts</a>";
    }
}
