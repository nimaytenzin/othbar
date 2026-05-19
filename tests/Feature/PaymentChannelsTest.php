<?php

namespace Tests\Feature;

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

    public function test_merchant_account_is_single_receiving_account(): void
    {
        $account = PaymentChannels::merchantAccount();

        $this->assertSame('10101-00123456-78', $account['account_number']);
        $this->assertSame('Bank of Bhutan (BoB)', $account['bank_label']);
        $this->assertArrayNotHasKey('id', $account);
    }
}
