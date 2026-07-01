<?php

declare(strict_types=1);

namespace Modules\Shipping\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Modules\Product\Models\Product;
use Modules\Settings\Models\Setting;
use Modules\Shipping\Actions\CalculateShippingAction;
use Throwable;

class CorreiosShippingService
{
    private ?array $settings = null;

    public function __construct(
        private readonly CalculateShippingAction $fallbackShipping
    ) {
    }

    public function quoteProduct(Product $product, string $destinationCep, int $quantity = 1, float $orderTotal = 0.0): array
    {
        return $this->quoteItems([
            ['product' => $product, 'quantity' => max(1, $quantity)],
        ], $destinationCep, $orderTotal ?: (float) $product->price * max(1, $quantity));
    }

    public function quoteCart(iterable $cartItems, string $destinationCep, float $orderTotal = 0.0): array
    {
        $items = [];

        foreach ($cartItems as $cartItem) {
            if (! $cartItem->product) {
                continue;
            }

            $items[] = [
                'product' => $cartItem->product,
                'quantity' => max(1, (int) $cartItem->quantity),
            ];
        }

        return $this->quoteItems($items, $destinationCep, $orderTotal);
    }

    private function quoteItems(array $items, string $destinationCep, float $orderTotal): array
    {
        $destinationCep = $this->onlyDigits($destinationCep);

        if (strlen($destinationCep) !== 8) {
            throw new InvalidArgumentException('Informe um CEP valido com 8 digitos.');
        }

        $physicalItems = array_values(array_filter($items, function (array $item): bool {
            $product = $item['product'] ?? null;

            return $product instanceof Product
                && (! method_exists($product, 'requiresShipping') || $product->requiresShipping());
        }));

        if (empty($physicalItems)) {
            return [
                'source' => 'digital',
                'methods' => [[
                    'id' => 'digital',
                    'name' => 'Entrega digital',
                    'price' => 0.0,
                    'formatted_price' => 'R$ 0,00',
                    'estimated_days' => 0,
                    'deadline' => 'Imediata',
                    'provider' => 'Sistema',
                    'is_free' => true,
                ]],
            ];
        }

        $package = $this->buildPackage($physicalItems);
        $officialMethods = $this->quoteOfficialCorreios($destinationCep, $package);

        if (! empty($officialMethods)) {
            return [
                'source' => 'correios_api',
                'package' => $package,
                'methods' => $officialMethods,
            ];
        }

        return [
            'source' => 'shipping_rules',
            'package' => $package,
            'methods' => $this->quoteInternalRules($destinationCep, $orderTotal),
        ];
    }

    private function quoteOfficialCorreios(string $destinationCep, array $package): array
    {
        if (! $this->boolOption('enabled', true)) {
            return [];
        }

        $originCep = $this->onlyDigits((string) $this->option('origin_cep', ''));
        $token = (string) $this->option('access_token', '');

        if (strlen($originCep) !== 8 || $token === '') {
            return [];
        }

        $methods = [];

        foreach ($this->serviceCodes() as $service) {
            try {
                $priceData = $this->requestPrice($service['code'], $originCep, $destinationCep, $package, $token);
                $price = $this->extractMoney($priceData);

                if ($price === null) {
                    continue;
                }

                $deadlineData = $this->requestDeadline($service['code'], $originCep, $destinationCep, $token);
                $days = $this->extractDays($deadlineData);

                $methods[] = [
                    'id' => 'correios-'.$service['code'],
                    'service_code' => $service['code'],
                    'name' => $service['name'],
                    'price' => $price,
                    'formatted_price' => $this->formatCurrency($price),
                    'estimated_days' => $days,
                    'deadline' => $days !== null ? $days.' dia(s) util(eis)' : 'Prazo sob consulta',
                    'provider' => 'Correios',
                    'is_free' => $price <= 0.0,
                ];
            } catch (Throwable $exception) {
                Log::warning('Falha ao consultar frete dos Correios.', [
                    'service_code' => $service['code'],
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $methods;
    }

    private function requestPrice(string $serviceCode, string $originCep, string $destinationCep, array $package, string $token): array
    {
        $query = [
            'cepOrigem' => $originCep,
            'cepDestino' => $destinationCep,
            'psObjeto' => (string) $package['weight_grams'],
            'tpObjeto' => '2',
            'comprimento' => (string) $package['length_cm'],
            'largura' => (string) $package['width_cm'],
            'altura' => (string) $package['height_cm'],
        ];

        if ($this->boolOption('contract_enabled', false)) {
            $contractNumber = (string) $this->option('contract_number', '');
            $regionalCode = (string) $this->option('regional_code', '');

            if ($contractNumber !== '') {
                $query['nuContrato'] = $contractNumber;
            }

            if ($regionalCode !== '') {
                $query['nuDR'] = $regionalCode;
            }
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout((int) $this->option('timeout', 8))
            ->get(rtrim((string) $this->option('preco_base_url'), '/').'/nacional/'.$serviceCode, $query);

        if (! $response->successful()) {
            throw new InvalidArgumentException('API de preco retornou HTTP '.$response->status());
        }

        return (array) $response->json();
    }

    private function requestDeadline(string $serviceCode, string $originCep, string $destinationCep, string $token): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout((int) $this->option('timeout', 8))
            ->get(rtrim((string) $this->option('prazo_base_url'), '/').'/nacional/'.$serviceCode, [
                'cepOrigem' => $originCep,
                'cepDestino' => $destinationCep,
            ]);

        if (! $response->successful()) {
            return [];
        }

        return (array) $response->json();
    }

    private function quoteInternalRules(string $destinationCep, float $orderTotal): array
    {
        $result = $this->fallbackShipping->execute(
            country: 'BR',
            region: null,
            postalCode: $destinationCep,
            orderTotal: $orderTotal
        );

        return collect($result['methods'] ?? [])
            ->map(function (array $method): array {
                $price = (float) ($method['price'] ?? 0);
                $days = isset($method['estimated_days']) ? (int) $method['estimated_days'] : null;

                return [
                    'id' => $method['id'] ?? null,
                    'zone_method_id' => $method['zone_method_id'] ?? null,
                    'name' => $method['name'] ?? 'Frete da loja',
                    'price' => $price,
                    'formatted_price' => $this->formatCurrency($price),
                    'estimated_days' => $days,
                    'deadline' => $days !== null ? $days.' dia(s) util(eis)' : 'Prazo configurado pela loja',
                    'provider' => 'Regras internas',
                    'is_free' => (bool) ($method['is_free'] ?? $price <= 0.0),
                ];
            })
            ->values()
            ->all();
    }

    private function buildPackage(array $items): array
    {
        $weight = 0;
        $length = (int) $this->option('default_length_cm', 20);
        $width = (int) $this->option('default_width_cm', 15);
        $height = (int) $this->option('default_height_cm', 5);

        foreach ($items as $item) {
            /** @var Product $product */
            $product = $item['product'];
            $quantity = max(1, (int) $item['quantity']);

            $weight += $this->productWeightGrams($product) * $quantity;
            $length = max($length, (int) round($this->productDimensionCm($product, ['length', 'comprimento']) ?? $length));
            $width = max($width, (int) round($this->productDimensionCm($product, ['width', 'largura']) ?? $width));
            $height = max($height, (int) round($this->productDimensionCm($product, ['height', 'altura']) ?? $height));
        }

        return [
            'weight_grams' => max((int) $this->option('default_weight_grams', 300), $weight),
            'length_cm' => max((int) config('shipping.correios.min_length_cm', 16), $length),
            'width_cm' => max((int) config('shipping.correios.min_width_cm', 11), $width),
            'height_cm' => max((int) config('shipping.correios.min_height_cm', 2), $height),
        ];
    }

    private function productWeightGrams(Product $product): int
    {
        $weight = $this->productDimensionCm($product, ['weight', 'peso']);

        if ($weight === null || $weight <= 0) {
            return (int) $this->option('default_weight_grams', 300);
        }

        if ((string) $this->option('weight_unit', 'kg') === 'g') {
            return max(1, (int) round($weight));
        }

        return max(1, (int) round($weight * 1000));
    }

    private function productDimensionCm(Product $product, array $keys): ?float
    {
        $product->loadMissing('attributeValues.attribute');

        foreach ($product->attributeValues as $attributeValue) {
            $attribute = $attributeValue->attribute;
            $code = strtolower((string) ($attribute?->code ?? ''));
            $name = strtolower((string) ($attribute?->name ?? ''));

            if (! in_array($code, $keys, true) && ! in_array($name, $keys, true)) {
                continue;
            }

            $value = method_exists($attributeValue, 'getValue')
                ? $attributeValue->getValue()
                : ($attributeValue->decimal_value
                    ?? $attributeValue->float_value
                    ?? $attributeValue->integer_value
                    ?? $attributeValue->string_value
                    ?? $attributeValue->text_value);

            if ($value === null || $value === '') {
                continue;
            }

            $numeric = (float) str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', (string) $value));

            if ($numeric > 0) {
                return $numeric;
            }
        }

        return null;
    }

    private function extractMoney(array $payload): ?float
    {
        return $this->extractFirstNumeric($payload, [
            'pcFinal',
            'precoFinal',
            'valorFinal',
            'valor',
            'preco',
            'pcProduto',
        ]);
    }

    private function extractDays(array $payload): ?int
    {
        $days = $this->extractFirstNumeric($payload, [
            'prazoEntrega',
            'prazo',
            'dias',
            'delivery_time',
        ]);

        return $days !== null ? (int) round($days) : null;
    }

    private function extractFirstNumeric(array $payload, array $keys): ?float
    {
        foreach ($payload as $key => $value) {
            if (in_array((string) $key, $keys, true) && is_scalar($value)) {
                return (float) str_replace(',', '.', (string) $value);
            }

            if (is_array($value)) {
                $nested = $this->extractFirstNumeric($value, $keys);

                if ($nested !== null) {
                    return $nested;
                }
            }
        }

        return null;
    }

    private function serviceCodes(): array
    {
        $raw = $this->option('service_codes', '03220:SEDEX,03298:PAC');

        if (is_array($raw)) {
            return $raw;
        }

        return collect(explode(',', (string) $raw))
            ->map(function (string $item): ?array {
                $parts = array_map('trim', explode(':', $item, 2));
                $code = $this->onlyDigits($parts[0] ?? '');

                if ($code === '') {
                    return null;
                }

                return [
                    'code' => $code,
                    'name' => $parts[1] ?? 'Correios '.$code,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function option(string $key, mixed $default = null): mixed
    {
        $settingsKey = 'correios_'.$key;
        $settings = $this->settings();

        if (array_key_exists($settingsKey, $settings) && $settings[$settingsKey] !== null && $settings[$settingsKey] !== '') {
            return $settings[$settingsKey];
        }

        return config('shipping.correios.'.$key, $default);
    }

    private function boolOption(string $key, bool $default = false): bool
    {
        return filter_var($this->option($key, $default), FILTER_VALIDATE_BOOL);
    }

    private function settings(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        try {
            $this->settings = Setting::query()->first()?->shipping_settings ?? [];
        } catch (Throwable) {
            $this->settings = [];
        }

        return $this->settings;
    }

    private function onlyDigits(string $value): string
    {
        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function formatCurrency(float $value): string
    {
        return 'R$ '.number_format($value, 2, ',', '.');
    }
}
