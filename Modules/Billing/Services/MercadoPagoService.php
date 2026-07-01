<?php

declare(strict_types=1);

namespace Modules\Billing\Services;

use Illuminate\Support\Facades\Log;

class MercadoPagoService
{
    private string $accessToken;

    private string $baseUrl;

    private const FEE_PERCENTAGE = 3.99;

    private const FEE_FIXED = 0.50;

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token', env('MERCADO_PAGO_ACCESS_TOKEN', ''));
        $this->baseUrl = env('MERCADO_PAGO_SANDBOX', true)
            ? 'https://api.mercadopago.com'
            : 'https://api.mercadopago.com';
    }

    public function createPreference(array $items, array $payer = [], ?string $returnUrl = null, ?string $webhookUrl = null): ?array
    {
        $payload = [
            'items' => array_map(fn ($item) => [
                'title' => $item['title'] ?? 'Produto',
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit_price' => (float) ($item['unit_price'] ?? 0),
                'currency_id' => 'BRL',
            ], $items),
            'payer' => $payer ?: ['email' => 'comprador@email.com'],
            'back_urls' => [
                'success' => $returnUrl ?? route('mercadopago.success'),
                'failure' => route('mercadopago.failure'),
                'pending' => route('mercadopago.pending'),
            ],
            'auto_return' => 'approved',
            'notification_url' => $webhookUrl ?? route('mercadopago.webhook'),
            'statement_descriptor' => 'LOJA RATAPLAM',
        ];

        return $this->post('/checkout/preferences', $payload);
    }

    public function getPayment(int $paymentId): ?array
    {
        return $this->get("/v1/payments/{$paymentId}");
    }

    public function refund(int $paymentId, ?float $amount = null): ?array
    {
        if ($amount !== null) {
            $payload = ['amount' => $amount];
            return $this->post("/v1/payments/{$paymentId}/refunds", $payload);
        }

        return $this->post("/v1/payments/{$paymentId}/refunds", []);
    }

    public function calculateFee(float $amount): float
    {
        return round($amount * (self::FEE_PERCENTAGE / 100) + self::FEE_FIXED, 2);
    }

    public function calculateTotalWithFee(float $amount): float
    {
        return round($amount + $this->calculateFee($amount), 2);
    }

    private function post(string $endpoint, array $data = []): ?array
    {
        return $this->request('POST', $endpoint, $data);
    }

    private function get(string $endpoint): ?array
    {
        return $this->request('GET', $endpoint);
    }

    private function request(string $method, string $endpoint, array $data = []): ?array
    {
        if (empty($this->accessToken)) {
            Log::error('MercadoPago: access token not configured');
            return null;
        }

        $ch = curl_init($this->baseUrl . $endpoint);

        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Log::error('MercadoPago cURL error: ' . $error);
            return null;
        }

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            Log::error('MercadoPago API error', [
                'http_code' => $httpCode,
                'response' => $result,
                'endpoint' => $endpoint,
            ]);
            return null;
        }

        return $result;
    }
}
