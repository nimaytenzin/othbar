<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class BankAccountFromSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::query()->find(1);

        if ($settings === null) {
            return;
        }

        if (BankAccount::query()->exists()) {
            if ($settings->default_bank_account_id === null) {
                $default = BankAccount::query()->where('is_default', true)->first()
                    ?? BankAccount::query()->first();

                if ($default !== null) {
                    $settings->update(['default_bank_account_id' => $default->id]);
                    SiteSetting::clearCache();
                }
            }

            return;
        }

        $merchant = $settings->payment_merchant_account ?? [];

        if (blank($merchant['account_number'] ?? null)) {
            return;
        }

        $account = BankAccount::query()->create([
            'label' => 'Primary receiving account',
            'bank_name' => $merchant['bank_label'] ?? 'Bank',
            'account_name' => $merchant['account_name'] ?? $settings->business_name ?? 'Othbar',
            'account_number' => $merchant['account_number'],
            'qr_path' => $merchant['qr_path'] ?? null,
            'is_default' => true,
            'is_active' => true,
        ]);

        $settings->update(['default_bank_account_id' => $account->id]);
        SiteSetting::clearCache();
    }
}
