<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Bhutan mobile payment apps (customer selects which app they paid with)
    |--------------------------------------------------------------------------
    */

    'apps' => [
        ['id' => 'mbob', 'label' => 'mBoB', 'bank_label' => 'Bank of Bhutan (BoB)'],
        ['id' => 'mpay', 'label' => 'mPAY', 'bank_label' => 'Bhutan National Bank (BNB)'],
        ['id' => 'epay', 'label' => 'ePay', 'bank_label' => 'Bhutan Development Bank (BDBL)'],
        ['id' => 'tpay', 'label' => 'T-Pay', 'bank_label' => 'T Bank Limited'],
        ['id' => 'druk_pnb', 'label' => 'Druk PNB Mobile', 'bank_label' => 'Druk PNB Bank'],
        ['id' => 'dkapp', 'label' => 'DK App', 'bank_label' => 'Digital Kidu Bank (DK)'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Merchant receiving account (one account — shown on pay page & receipts)
    |--------------------------------------------------------------------------
    */

    'merchant_account' => [
        'bank_label' => 'Bank of Bhutan (BoB)',
        'account_name' => 'Othbar Horticulture Project',
        'account_number' => '10101-00123456-78',
        'qr_public_path' => 'images/payment-qr/mbob.png',
    ],

    'pickup_address_label' => 'In-store pickup at Othbar',

];
