<?php

declare(strict_types=1);

return [
    'enabled' => env('MERCADOPAGO_ENABLED', false),
    'environment' => env('MERCADOPAGO_ENVIRONMENT', 'sandbox'),
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    'statement_descriptor' => env('MERCADOPAGO_STATEMENT_DESCRIPTOR', 'LOJA VIRTUAL'),
    'base_url' => env('MERCADOPAGO_BASE_URL', 'https://api.mercadopago.com'),
    'checkout_timeout' => env('MERCADOPAGO_TIMEOUT', 20),
];
