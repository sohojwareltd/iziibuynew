<?php

declare(strict_types=1);

return [
    'site' => [
        'email' => env('SITE_EMAIL', 'admin@example.com'),
        'security_key' => env('SITE_SECURITY_KEY', ''),
        'title' => env('APP_NAME', 'Iziibuy'),
    ],
    'payment' => [
        'api_key' => env('QUICKPAY_API_KEY', ''),
        'private_key' => env('QUICKPAY_PRIVATE_KEY', ''),
        'payment_method_fee' => env('PAYMENT_METHOD_FEE', 0),
        'registration_tax' => env('PAYMENT_REGISTRATION_TAX', 0),
        'estibliment_cost' => env('PAYMENT_ESTABLISHMENT_COST', 1995),
        'price_per_shop' => env('PAYMENT_PRICE_PER_SHOP', 299),
        'service_establishment_cost' => env('PAYMENT_SERVICE_ESTABLISHMENT_COST', 1995),
        'service_monthly_cost' => env('PAYMENT_SERVICE_MONTHLY_COST', 299),
        'price_per_user' => env('PAYMENT_PRICE_PER_USER', 99),
    ],
    'terms' => [
        'no' => env('TERMS_SUMMARY', ''),
    ],
];
