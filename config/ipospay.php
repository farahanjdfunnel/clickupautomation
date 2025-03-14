<?php

return [
    'environment' => env('IPOSPAY_ENVIRONMENT', 'sandbox'),  // Default to 'sandbox' if not set in .env

    'sandbox' => [
        'spin_url' => 'https://test.spinpos.net/spin/v2',
        'hpp_url'   => 'https://payment.ipospays.tech/api/v1',
        'transact_url' => 'https://payment.ipospays.tech/api/v1'

    ],

    'live' => [
        'spin_url' => 'https://api.spinpos.net/v2',
        'hpp_url'   => 'https://payment.ipospays.com/api/v1',
        'transact_url' => 'https://payment.ipospays.com/api/v1'
    ],
];
