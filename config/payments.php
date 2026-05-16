<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Static payment QR images (per channel)
    |--------------------------------------------------------------------------
    |
    | Place your real MBOB / EPAY / DKBANK QR PNG files under public/images/payment-qr/
    | (or another public path) and set filename here. If missing, the pay page shows copy-only.
    |
    */

    'channels' => [
        'mbob' => [
            'id' => 'mbob',
            'label' => 'MBOB',
            'bank_label' => 'Bank of Bhutan (BOB)',
            'account_name' => 'Othbar Horticulture Project',
            'account_number' => '10101-00123456-78',
            'qr_public_path' => 'images/payment-qr/mbob.png',
        ],
        'epay' => [
            'id' => 'epay',
            'label' => 'EPAY',
            'bank_label' => 'Bhutan National Bank (BNB)',
            'account_name' => 'Othbar Horticulture Project',
            'account_number' => '201-00-987654-3',
            'qr_public_path' => 'images/payment-qr/epay.png',
        ],
        'dkbank' => [
            'id' => 'dkbank',
            'label' => 'DKBANK',
            'bank_label' => 'Druk PNB Bank Ltd',
            'account_name' => 'Othbar Horticulture Project',
            'account_number' => 'DK-0042-00543210',
            'qr_public_path' => 'images/payment-qr/dkbank.png',
        ],
    ],

    'pickup_address_label' => 'In-store pickup at Othbar',

];
