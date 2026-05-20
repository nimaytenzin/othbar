<?php

namespace App\Support;

use App\Models\BankAccount;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class PaymentChannels
{
    /**
     * Mobile apps customers can pay with (fixed list — not tied to account numbers).
     *
     * @return list<array{id: string, label: string, bank_label: string}>
     */
    public static function paymentApps(): array
    {
        return collect(config('payments.apps', []))
            ->map(fn (array $app): array => [
                'id' => (string) ($app['id'] ?? ''),
                'label' => (string) ($app['label'] ?? ''),
                'bank_label' => (string) ($app['bank_label'] ?? ''),
            ])
            ->filter(fn (array $app): bool => filled($app['id']) && filled($app['label']))
            ->values()
            ->all();
    }

    /**
     * Default active bank account for storefront checkout and receipts.
     */
    public static function defaultBankAccount(): ?BankAccount
    {
        $settings = SiteSetting::current();
        $settings->loadMissing('defaultBankAccount');

        if ($settings->defaultBankAccount?->is_active) {
            return $settings->defaultBankAccount;
        }

        return BankAccount::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }

    /**
     * Receiving account details for storefront pay page and receipts.
     *
     * @return array{bank_label: string, account_name: string, account_number: string, qr_url: ?string}
     */
    public static function merchantAccount(): array
    {
        $account = static::defaultBankAccount();

        if ($account !== null) {
            return static::normalizeMerchantAccount([
                'bank_label' => $account->bank_name,
                'account_name' => $account->account_name,
                'account_number' => $account->account_number,
                'qr_path' => $account->qr_path,
            ]);
        }

        return static::normalizeMerchantAccount(config('payments.merchant_account', []));
    }

    /**
     * @deprecated Use paymentApps() or merchantAccount().
     *
     * @return list<array{id: string, label: string, bank_label: string, account_name: string, account_number: string, qr_url: ?string}>
     */
    public static function forStorefront(): array
    {
        return static::paymentApps();
    }

    /**
     * @param  array<string, mixed>  $account
     * @return array{bank_label: string, account_name: string, account_number: string, qr_url: ?string}
     */
    protected static function normalizeMerchantAccount(array $account): array
    {
        $qrUrl = null;

        if (! empty($account['qr_path'])) {
            $qrUrl = Storage::disk('public')->url((string) $account['qr_path']);
        } elseif (! empty($account['qr_public_path'])) {
            $qrUrl = asset((string) $account['qr_public_path']);
        }

        return [
            'bank_label' => (string) ($account['bank_label'] ?? ''),
            'account_name' => (string) ($account['account_name'] ?? ''),
            'account_number' => (string) ($account['account_number'] ?? ''),
            'qr_url' => $qrUrl,
        ];
    }
}
