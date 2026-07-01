<?php

declare(strict_types=1);

return [
    'name' => 'Shipping',

    'correios' => [
        'enabled' => env('CORREIOS_ENABLED', true),
        'contract_enabled' => env('CORREIOS_CONTRACT_ENABLED', false),
        'origin_cep' => env('CORREIOS_ORIGIN_CEP', ''),
        'access_token' => env('CORREIOS_ACCESS_TOKEN', ''),
        'contract_number' => env('CORREIOS_CONTRACT_NUMBER', ''),
        'regional_code' => env('CORREIOS_REGIONAL_CODE', ''),
        'preco_base_url' => env('CORREIOS_PRECO_BASE_URL', 'https://api.correios.com.br/preco/v1'),
        'prazo_base_url' => env('CORREIOS_PRAZO_BASE_URL', 'https://api.correios.com.br/prazo/v1'),
        'service_codes' => env('CORREIOS_SERVICE_CODES', '03220:SEDEX,03298:PAC'),
        'timeout' => (int) env('CORREIOS_TIMEOUT', 8),
        'default_weight_grams' => (int) env('CORREIOS_DEFAULT_WEIGHT_GRAMS', 300),
        'weight_unit' => env('CORREIOS_WEIGHT_UNIT', 'kg'),
        'default_length_cm' => (int) env('CORREIOS_DEFAULT_LENGTH_CM', 20),
        'default_width_cm' => (int) env('CORREIOS_DEFAULT_WIDTH_CM', 15),
        'default_height_cm' => (int) env('CORREIOS_DEFAULT_HEIGHT_CM', 5),
        'min_length_cm' => 16,
        'min_width_cm' => 11,
        'min_height_cm' => 2,
    ],
];
