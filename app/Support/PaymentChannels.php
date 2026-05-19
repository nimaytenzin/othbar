<?php

namespace App\Support;

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
     * Single merchant receiving account (admin configures one bank account).
     *
     * @return array{bank_label: string, account_name: string, account_number: string, qr_url: ?string}
     */
    public static function merchantAccount(): array
    {
        $settings = SiteSetting::current();
        $stored = $settings->payment_merchant_account;

        if (is_array($stored) && filled($stored['account_number'] ?? null)) {
            return static::normalizeMerchantAccount($stored);
        }

        $legacyChannels = $settings->payment_channels;
        if (is_array($legacyChannels) && $legacyChannels !== []) {
            $first = collect($legacyChannels)->first(
                fn (array $channel): bool => filled($channel['account_number'] ?? null),
            );

            if (is_array($first)) {
                return static::normalizeMerchantAccount($first);
            }
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
