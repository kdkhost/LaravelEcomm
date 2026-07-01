<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Services\MercadoPagoService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;

class MercadoPagoController extends CoreController
{
    public function __construct(
        private MercadoPagoService $service,
    ) {}

    public function checkout(): RedirectResponse
    {
        $pendingOrder = session('pending_order');

        if (!$pendingOrder) {
            return redirect()->route('front.checkout')->with('error', 'Nenhum pedido pendente encontrado.');
        }

        $totalAmount = $pendingOrder['total_amount'] ?? 0;

        try {
            $preference = $this->service->createPreference(
                items: [
                    [
                        'title' => 'Pedido Loja Rataplam',
                        'quantity' => 1,
                        'unit_price' => $totalAmount,
                    ],
                ],
                payer: [
                    'email' => $pendingOrder['email'] ?? 'comprador@email.com',
                ],
                returnUrl: route('mercadopago.success'),
                webhookUrl: route('mercadopago.webhook'),
            );

            if (!$preference || !isset($preference['init_point'])) {
                return redirect()->route('front.checkout')->with('error', 'Erro ao criar pagamento no MercadoPago.');
            }

            return redirect()->away($preference['init_point']);
        } catch (Exception $e) {
            return redirect()->route('front.checkout')->with('error', 'Erro no MercadoPago: ' . $e->getMessage());
        }
    }

    public function success(Request $request, StoreOrderAction $storeOrderAction): RedirectResponse
    {
        $paymentId = $request->input('payment_id');
        $pendingOrder = session('pending_order');

        if (!$pendingOrder) {
            return redirect()->route('front.index')->with('error', 'Nenhum pedido pendente encontrado.');
        }

        try {
            $userId = $pendingOrder['user_id'] ?? '';
            $cartItems = Helper::getAllProductFromCart($userId);

            if ($cartItems->isEmpty()) {
                return redirect()->route('front.cart')->with('error', 'Seu carrinho está vazio.');
            }

            $pendingOrder['payment_status'] = 'paid';
            $pendingOrder['payment_method'] = 'mercadopago';
            $pendingOrder['transaction_reference'] = $paymentId;

            $order = $storeOrderAction->execute(OrderDTO::fromArray($pendingOrder));

            foreach ($cartItems as $cartItem) {
                $cartItem->update(['order_id' => $order->id]);
            }

            if (!empty($pendingOrder['user_id']) && !empty($pendingOrder['save_address'])) {
                $user = \Modules\User\Models\User::find($pendingOrder['user_id']);
                if ($user) {
                    $user->addresses()->create([
                        'type' => 'shipping',
                        'is_default' => !empty($pendingOrder['make_default_address']),
                        'first_name' => $pendingOrder['first_name'],
                        'last_name' => $pendingOrder['last_name'],
                        'email' => $pendingOrder['email'],
                        'phone' => $pendingOrder['phone'],
                        'country' => $pendingOrder['country'],
                        'city' => $pendingOrder['city'],
                        'address1' => $pendingOrder['address1'],
                        'address2' => $pendingOrder['address2'] ?? null,
                        'post_code' => $pendingOrder['post_code'],
                    ]);
                }
            }

            session()->forget(['cart', 'coupon', 'pending_order']);

            return redirect()->route('front.index')->with('success', 'Pagamento aprovado! Pedido #' . $order->order_number);
        } catch (Exception $e) {
            return redirect()->route('front.index')->with('error', 'Erro ao finalizar pedido: ' . $e->getMessage());
        }
    }

    public function failure(): RedirectResponse
    {
        return redirect()->route('front.checkout')->with('error', 'Pagamento não foi concluído. Tente novamente.');
    }

    public function pending(): RedirectResponse
    {
        $pendingOrder = session('pending_order');
        if ($pendingOrder) {
            return redirect()->route('front.index')->with('info', 'Seu pagamento está sendo processado. Em breve confirmaremos.');
        }
        return redirect()->route('front.checkout');
    }

    public function webhook(Request $request): JsonResponse
    {
        $topic = $request->input('topic');
        $paymentId = $request->input('id') ? (int) $request->input('id') : null;

        if ($topic === 'payment' && $paymentId) {
            $payment = $this->service->getPayment($paymentId);

            if ($payment && isset($payment['status'])) {
                $status = $payment['status'];
                $externalRef = $payment['external_reference'] ?? null;
                $transactionId = $payment['id'] ?? null;

                $order = null;
                if ($externalRef) {
                    $order = \Modules\Order\Models\Order::where('order_number', $externalRef)->first();
                }

                if ($status === 'approved') {
                    if ($order) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'transaction_reference' => $transactionId,
                        ]);
                    }
                } elseif (in_array($status, ['cancelled', 'refunded', 'charged_back'])) {
                    if ($order) {
                        $order->update([
                            'payment_status' => 'unpaid',
                            'status' => 'cancelled',
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function refund(Request $request, int $orderId): RedirectResponse
    {
        $order = \Modules\Order\Models\Order::findOrFail($orderId);
        $transactionRef = $order->transaction_reference;

        if (!$transactionRef) {
            return redirect()->back()->with('error', 'Nenhuma transação MercadoPago associada a este pedido.');
        }

        $partialAmount = $request->input('amount');

        try {
            if ($partialAmount) {
                $this->service->refund((int) $transactionRef, (float) $partialAmount);
                $order->update(['payment_status' => 'unpaid']);
            } else {
                $this->service->refund((int) $transactionRef);
                $order->update(['payment_status' => 'unpaid', 'status' => 'cancelled']);
            }

            return redirect()->back()->with('success', 'Reembolso processado com sucesso.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erro no reembolso: ' . $e->getMessage());
        }
    }
}
