<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Billing\Services\MercadoPagoService;
use Throwable;

class MercadoPagoController extends Controller
{
    public function __construct(
        private readonly MercadoPagoService $mercadoPagoService
    ) {}

    public function retorno(Request $request): RedirectResponse
    {
        $paymentId = $request->query('payment_id')
            ?: $request->query('collection_id')
            ?: $request->query('id');

        if ($paymentId) {
            try {
                $result = $this->mercadoPagoService->syncPayment((string) $paymentId);
                $order = $result['order'];

                if ($order && $order->payment_status === 'paid') {
                    session()->forget(['cart', 'coupon', 'pending_order']);

                    return redirect()->route('front.my-orders', ['locale' => app()->getLocale()])
                        ->with('success', 'Pagamento aprovado no Mercado Pago. Pedido '.$order->order_number.' atualizado automaticamente.');
                }

                return redirect()->route('front.my-orders', ['locale' => app()->getLocale()])
                    ->with('warning', 'Pagamento recebido pelo Mercado Pago e aguardando confirmação.');
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('front.checkout')
                    ->with('error', 'Não foi possível confirmar o pagamento agora. O webhook ainda pode atualizar o pedido automaticamente.');
            }
        }

        return redirect()->route('front.my-orders', ['locale' => app()->getLocale()])
            ->with('warning', 'Retorno do Mercado Pago recebido sem identificador de pagamento.');
    }

    public function webhook(Request $request, ?string $secret = null): JsonResponse
    {
        $payload = $request->all();
        $eventType = (string) ($payload['type'] ?? $payload['topic'] ?? $request->query('topic', 'unknown'));
        $resourceId = $this->extractResourceId($payload, $request);

        if (! $this->isValidSecret($request, $secret, $resourceId)) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $logId = DB::table('mercadopago_webhook_logs')->insertGetId([
            'event_type' => $eventType,
            'resource_id' => $resourceId,
            'status' => 'received',
            'headers' => json_encode($request->headers->all()),
            'payload' => json_encode($payload),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! $resourceId || ! in_array($eventType, ['payment', 'topic_payment', 'unknown'], true)) {
            DB::table('mercadopago_webhook_logs')->where('id', $logId)->update([
                'status' => 'ignored',
                'processed_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'ignored']);
        }

        try {
            $result = $this->mercadoPagoService->syncPayment($resourceId);
            $externalReference = Arr::get($result['payment'], 'external_reference');

            DB::table('mercadopago_webhook_logs')->where('id', $logId)->update([
                'external_reference' => $externalReference,
                'status' => $result['order'] ? 'processed' : 'order_not_found',
                'processed_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            DB::table('mercadopago_webhook_logs')->where('id', $logId)->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'processed_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function isValidSecret(Request $request, ?string $secret, ?string $resourceId): bool
    {
        $expected = $this->mercadoPagoService->webhookSecret();

        if ($expected === '') {
            return true;
        }

        $provided = $secret ?: (string) $request->query('secret', '');

        if ($provided !== '' && hash_equals($expected, $provided)) {
            return true;
        }

        return $this->hasValidSignature($request, $resourceId, $expected);
    }

    private function hasValidSignature(Request $request, ?string $resourceId, string $secret): bool
    {
        $signature = (string) $request->header('x-signature', '');
        $requestId = (string) $request->header('x-request-id', '');

        if ($signature === '') {
            return false;
        }

        $parts = $this->parseSignatureHeader($signature);
        $timestamp = $parts['ts'] ?? '';
        $receivedHash = $parts['v1'] ?? '';

        if ($timestamp === '' || $receivedHash === '') {
            return false;
        }

        $manifest = '';
        if ($resourceId) {
            $manifest .= 'id:'.$resourceId.';';
        }
        if ($requestId !== '') {
            $manifest .= 'request-id:'.$requestId.';';
        }
        $manifest .= 'ts:'.$timestamp.';';

        $calculatedHash = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($calculatedHash, $receivedHash);
    }

    /**
     * @return array<string, string>
     */
    private function parseSignatureHeader(string $signature): array
    {
        $parts = [];

        foreach (explode(',', $signature) as $piece) {
            [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, '');
            if ($key !== '' && $value !== '') {
                $parts[$key] = $value;
            }
        }

        return $parts;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractResourceId(array $payload, Request $request): ?string
    {
        $id = Arr::get($payload, 'data.id')
            ?: Arr::get($payload, 'data_id')
            ?: Arr::get($payload, 'id')
            ?: $request->query('data_id')
            ?: $request->query('id');

        return $id ? (string) $id : null;
    }
}
