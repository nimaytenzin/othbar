<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Support\PaymentChannels;
use App\Support\PaymentMethods;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentChannelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_apps_include_all_bhutan_mobile_banking_apps(): void
    {
        $apps = PaymentMethods::bankChannels();

        $this->assertCount(6, $apps);

        $this->assertSame([
            'mbob',
            'mpay',
            'epay',
            'tpay',
            'druk_pnb',
            'dkapp',
        ], collect($apps)->pluck('id')->all());

        $this->assertSame('mBoB', collect($apps)->firstWhere('id', 'mbob')['label']);
        $this->assertSame('Bank of Bhutan (BoB)', collect($apps)->firstWhere('id', 'mbob')['bank_label']);
        $this->assertSame('mPAY', collect($apps)->firstWhere('id', 'mpay')['label']);
        $this->assertSame('Bhutan National Bank (BNB)', collect($apps)->firstWhere('id', 'mpay')['bank_label']);
        $this->assertSame('ePay', collect($apps)->firstWhere('id', 'epay')['label']);
        $this->assertSame('Bhutan Development Bank (BDBL)', collect($apps)->firstWhere('id', 'epay')['bank_label']);
    }

    public function test_merchant_account_falls_back_to_config_when_no_bank_accounts(): void
    {
        $account = PaymentChannels::merchantAccount();

        $this->assertSame('10101-00123456-78', $account['account_number']);
        $this->assertSame('Bank of Bhutan (BoB)', $account['bank_label']);
        $this->assertArrayNotHasKey('id', $account);
    }

    public function test_merchant_account_uses_default_bank_account_from_database(): void
    {
        BankAccount::query()->create([
            'bank_name' => 'Bhutan National Bank',
            'account_name' => 'Othbar Trading',
            'account_number' => 'BNB-99988877',
            'is_default' => true,
            'is_active' => true,
        ]);

        $account = PaymentChannels::merchantAccount();

        $this->assertSame('BNB-99988877', $account['account_number']);
        $this->assertSame('Bhutan National Bank', $account['bank_label']);
    }
}
