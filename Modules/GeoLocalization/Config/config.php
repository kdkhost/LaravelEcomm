<?php

declare(strict_types=1);

return [
    'name' => 'GeoLocalization',

    /*
    |--------------------------------------------------------------------------
    | Enable GeoIP Detection
    |--------------------------------------------------------------------------
    */
    'enabled' => env('GEOIP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | GeoIP Provider
    |--------------------------------------------------------------------------
    |
    | Supported: "database", "ipapi", "ipgeolocation"
    |
    */
    'provider' => env('GEOIP_PROVIDER', 'ipapi'),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration (seconds)
    |--------------------------------------------------------------------------
    */
    'cache_duration' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Auto-detect Locale from Country
    |--------------------------------------------------------------------------
    */
    'auto_locale' => true,

    /*
    |--------------------------------------------------------------------------
    | Expose Location Headers in Response
    |--------------------------------------------------------------------------
    */
    'expose_headers' => env('GEOIP_EXPOSE_HEADERS', false),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    */
    'base_currency' => env('BASE_CURRENCY', 'BRL'),

    'currency_provider' => env('CURRENCY_PROVIDER', 'exchangerate-api'),

    'currency_cache_duration' => 3600, // 1 hour

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    */
    'ipgeolocation_api_key' => env('IPGEOLOCATION_API_KEY'),

    'exchangerate_api_key' => env('EXCHANGERATE_API_KEY'),

    'openexchangerates_api_key' => env('OPENEXCHANGERATES_API_KEY'),

    'fixer_api_key' => env('FIXER_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Location Settings
    |--------------------------------------------------------------------------
    */
    'default_country' => env('DEFAULT_COUNTRY', 'BR'),

    'default_currency' => env('DEFAULT_CURRENCY', 'BRL'),

    'default_timezone' => env('DEFAULT_TIMEZONE', 'America/Sao_Paulo'),
];
