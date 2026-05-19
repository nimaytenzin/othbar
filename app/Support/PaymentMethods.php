<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class PaymentMethods
{
    public const MODE_CASH = 'cash';

    public const MODE_BANK_TRANSFER = 'bank-transfer';

    /**
     * @return list<string>
     */
    public static function modes(): array
    {
        return [
            self::MODE_CASH,
            self::MODE_BANK_TRANSFER,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function modeLabels(): array
    {
        return [
            self::MODE_CASH => 'Cash',
            self::MODE_BANK_TRANSFER => 'Bank transfer',
        ];
    }

    /**
     * @return list<array{id: string, label: string, bank_label: string, account_name: string, account_number: string, qr_url: ?string}>
     */
    public static function bankChannels(): array
    {
        return PaymentChannels::paymentApps();
    }

    /**
     * @return array<string, string> id => label
     */
    public static function bankOptions(): array
    {
        $options = [];

        foreach (self::bankChannels() as $channel) {
            $options[$channel['id']] = $channel['label'];
        }

        return $options;
    }

    public static function paymentAppNames(): string
    {
        return collect(self::bankChannels())
            ->pluck('label')
            ->implode(', ');
    }

    public static function bankDisplayLabel(?string $bankId): ?string
    {
        if (! filled($bankId)) {
            return null;
        }

        foreach (self::bankChannels() as $channel) {
            if ($channel['id'] === $bankId) {
                return $channel['bank_label'] !== ''
                    ? "{$channel['label']} ({$channel['bank_label']})"
                    : $channel['label'];
            }
        }

        return self::legacyBankLabels()[$bankId] ?? strtoupper($bankId);
    }

    /**
     * @return array<string, string>
     */
    private static function legacyBankLabels(): array
    {
        return [
            'dkbank' => 'Druk PNB Mobile',
        ];
    }

    /**
     * @return list<string>
     */
    public static function bankIds(): array
    {
        return array_keys(self::bankOptions());
    }

    public static function bankLabel(?string $bankId): ?string
    {
        if (! filled($bankId)) {
            return null;
        }

        return self::bankOptions()[$bankId] ?? self::legacyBankLabels()[$bankId] ?? strtoupper($bankId);
    }

    public static function modeLabel(?string $mode): string
    {
        if (! filled($mode)) {
            return '—';
        }

        return self::modeLabels()[$mode] ?? str($mode)->replace('-', ' ')->title()->toString();
    }

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public static function paymentSummary(?array $metadata, ?string $paymentReference = null): string
    {
        $metadata = $metadata ?? [];
        $mode = (string) ($metadata['payment_method'] ?? '');
        $bankId = (string) ($metadata['payment_bank'] ?? '');
        $parts = [];

        if ($mode === self::MODE_CASH) {
            $parts[] = 'Cash';
        } elseif ($mode === self::MODE_BANK_TRANSFER) {
            $bank = self::bankDisplayLabel($bankId) ?? self::bankLabel($bankId);
            $parts[] = $bank ? "Bank transfer ({$bank})" : 'Bank transfer';
        } elseif ($mode !== '') {
            $parts[] = self::modeLabel($mode);
        }

        if (filled($paymentReference)) {
            $parts[] = "Ref: {$paymentReference}";
        }

        return $parts !== [] ? implode(' · ', $parts) : '—';
    }

    public static function validateCounterPayment(
        string $paymentMethod,
        ?string $paymentBank,
        ?string $paymentReference,
    ): void {
        if (! in_array($paymentMethod, self::modes(), true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Select a valid payment mode.',
            ]);
        }

        if ($paymentMethod === self::MODE_BANK_TRANSFER) {
            if (! filled($paymentBank) || ! in_array($paymentBank, self::bankIds(), true)) {
                throw ValidationException::withMessages([
                    'payment_bank' => 'Select the mobile banking app used for this transfer ('.self::paymentAppNames().').',
                ]);
            }

            if (! filled($paymentReference)) {
                throw ValidationException::withMessages([
                    'payment_reference' => 'Enter the transaction or journal number.',
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function metadataPayload(
        string $paymentMethod,
        ?string $paymentBank = null,
    ): array {
        $payload = [
            'payment_method' => $paymentMethod,
        ];

        if ($paymentMethod === self::MODE_BANK_TRANSFER && filled($paymentBank)) {
            $payload['payment_bank'] = $paymentBank;
        }

        return $payload;
    }
}
