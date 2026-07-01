<?php

declare(strict_types=1);

namespace Modules\Billing\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Modules\Billing\Models\Payment;
use Modules\Order\Models\Order;
use Modules\Settings\Models\Setting;

class MercadoPagoService
{
    public function isEnabled(): bool
    {
        return filter_var($this->setting('mercadopago_enabled', config('mercadopago.enabled'), false), FILTER_VALIDATE_BOOL)
            && $this->accessToken() !== '';
    }

    public function webhookUrl(): string
    {
        $secret = $this->webhookSecret();

        return $secret !== ''
            ? route('mercadopago.webhook').'?secret='.urlencode($secret)
            : route('mercadopago.webhook');
    }

    /**
     * @return array<string, mixed>
     */
    public function createPreference(Order $order): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Mercado Pago não está habilitado ou não possui Access Token configurado.');
        }

        $order->loadMissing('carts.product');

        $payload = [
            'items' => $this->itemsFromOrder($order),
            'payer' => $this->payerFromOrder($order),
            'external_reference' => $order->order_number,
            'statement_descriptor' => $this->statementDescriptor(),
            'notification_url' => $this->webhookUrl(),
            'back_urls' => [
                'success' => route('mercadopago.return', ['status' => 'success']),
                'pending' => route('mercadopago.return', ['status' => 'pending']),
                'failure' => route('mercadopago.return', ['status' => 'failure']),
            ],
            'auto_return' => 'approved',
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'payment_methods' => [
                'installments' => 12,
                'default_installments' => 1,
            ],
        ];

        $response = $this->client()
            ->withHeaders([
                'X-Idempotency-Key' => 'order-'.$order->id.'-'.$order->order_number,
            ])
            ->post('/checkout/preferences', $payload);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao criar preferência no Mercado Pago: '.$response->body());
        }

        $data = $response->json();
        $initPoint = $this->isProduction()
            ? Arr::get($data, 'init_point')
            : (Arr::get($data, 'sandbox_init_point') ?: Arr::get($data, 'init_point'));

        if (! $initPoint) {
            throw new RuntimeException('Mercado Pago não retornou URL de checkout.');
        }

        $order->update([
            'mercadopago_preference_id' => Arr::get($data, 'id'),
            'mercadopago_status' => 'preference_created',
            'mercadopago_payload' => $data,
        ]);

        return [
            'id' => Arr::get($data, 'id'),
            'init_point' => $initPoint,
            'payload' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getPayment(string $paymentId): array
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException('Mercado Pago não está habilitado.');
        }

        $response = $this->client()->get('/v1/payments/'.$paymentId);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao consultar pagamento Mercado Pago: '.$response->body());
        }

        return $response->json();
    }

    /**
     * @return array{order: Order|null, payment: array<string, mixed>}
     */
    public function syncPayment(string $paymentId): array
    {
        $paymentData = $this->getPayment($paymentId);
        $order = $this->findOrderFromPayment($paymentData);

        if (! $order) {
            return ['order' => null, 'payment' => $paymentData];
        }

        DB::transaction(function () use ($order, $paymentData, $paymentId): void {
            $mapped = $this->mapPaymentStatus((string) Arr::get($paymentData, 'status', 'pending'));

            $order->update([
                'payment_status' => $mapped['payment_status'],
                'status' => $mapped['order_status'],
                'transaction_reference' => $paymentId,
                'mercadopago_payment_id' => $paymentId,
                'mercadopago_status' => Arr::get($paymentData, 'status'),
                'mercadopago_payload' => $paymentData,
            ]);

            if ($order->user_id) {
                Payment::updateOrCreate(
                    ['transaction_id' => $paymentId],
                    [
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'payment_method' => 'mercadopago',
                        'status' => $mapped['payment_record_status'],
                        'amount' => (float) Arr::get($paymentData, 'transaction_amount', $order->total_amount),
                        'currency' => Arr::get($paymentData, 'currency_id', 'BRL'),
                        'transaction_reference' => $order->order_number,
                        'notes' => Arr::get($paymentData, 'status_detail'),
                        'metadata' => $paymentData,
                        'processed_at' => $mapped['payment_record_status'] === 'completed' ? now() : null,
                    ]
                );
            }
        });

        return ['order' => $order->fresh(), 'payment' => $paymentData];
    }

    public function webhookSecret(): string
    {
        return (string) $this->setting('mercadopago_webhook_secret', config('mercadopago.webhook_secret', ''), true);
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl((string) config('mercadopago.base_url'))
            ->acceptJson()
            ->asJson()
            ->withToken($this->accessToken())
            ->timeout((int) config('mercadopago.checkout_timeout', 20))
            ->retry(2, 250);
    }

    private function accessToken(): string
    {
        return trim((string) $this->setting('mercadopago_access_token', config('mercadopago.access_token', ''), true));
    }

    private function isProduction(): bool
    {
        return $this->setting('mercadopago_environment', config('mercadopago.environment'), true) === 'production';
    }

    private function statementDescriptor(): string
    {
        $descriptor = (string) $this->setting('mercadopago_statement_descriptor', config('mercadopago.statement_descriptor'), true);
        $descriptor = preg_replace('/[^A-Za-z0-9 ]/', '', $descriptor) ?: 'LOJA VIRTUAL';

        return mb_substr(mb_strtoupper($descriptor), 0, 22);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function itemsFromOrder(Order $order): array
    {
        $items = [];
        $itemsAmount = 0.0;

        foreach ($order->carts as $cart) {
            $quantity = max(1, (int) $cart->quantity);
            $unitPrice = round((float) $cart->price, 2);
            $itemsAmount += $quantity * $unitPrice;

            $items[] = [
                'id' => (string) $cart->product_id,
                'title' => $cart->product?->title ?: 'Produto '.$cart->product_id,
                'quantity' => $quantity,
                'currency_id' => 'BRL',
                'unit_price' => $unitPrice,
            ];
        }

        $orderTotal = round((float) $order->total_amount, 2);
        $difference = round($orderTotal - $itemsAmount, 2);

        if ($items === [] || $orderTotal < $itemsAmount) {
            return [[
                'id' => (string) $order->id,
                'title' => 'Pedido '.$order->order_number,
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => max(0.01, $orderTotal),
            ]];
        }

        if ($difference > 0) {
            $items[] = [
                'id' => 'frete-ajustes-'.$order->id,
                'title' => 'Frete e ajustes',
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => $difference,
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>
     */
    private function payerFromOrder(Order $order): array
    {
        return array_filter([
            'name' => $order->first_name,
            'surname' => $order->last_name,
            'email' => $order->email ?: $order->user?->email,
            'phone' => [
                'number' => preg_replace('/\D+/', '', (string) $order->phone),
            ],
            'address' => [
                'zip_code' => preg_replace('/\D+/', '', (string) $order->post_code),
                'street_name' => $order->address1,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $paymentData
     */
    private function findOrderFromPayment(array $paymentData): ?Order
    {
        $orderId = Arr::get($paymentData, 'metadata.order_id');
        if ($orderId) {
            return Order::find((int) $orderId);
        }

        $reference = Arr::get($paymentData, 'external_reference');
        if ($reference) {
            return Order::where('order_number', $reference)->first();
        }

        return null;
    }

    /**
     * @return array{payment_status: string, order_status: string, payment_record_status: string}
     */
    private function mapPaymentStatus(string $status): array
    {
        return match ($status) {
            'approved', 'authorized' => [
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'payment_record_status' => 'completed',
            ],
            'pending', 'in_process', 'in_mediation' => [
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_record_status' => 'processing',
            ],
            'refunded', 'charged_back', 'cancelled', 'rejected' => [
                'payment_status' => 'unpaid',
                'order_status' => 'cancelled',
                'payment_record_status' => $status === 'refunded' ? 'refunded' : 'failed',
            ],
            default => [
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'payment_record_status' => 'pending',
            ],
        };
    }

    private function setting(string $key, mixed $default = null, bool $fallbackWhenBlank = true): mixed
    {
        $settings = app()->bound('settings') ? app('settings') : Setting::first();

        if ($settings instanceof Setting) {
            $value = Arr::get($settings->payment_settings ?? [], $key);

            if ($fallbackWhenBlank && ($value === null || $value === '')) {
                return $default;
            }

            return $value ?? $default;
        }

        return $default;
    }
}
