<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Services\MercadoPagoService;
use Modules\Coupon\Actions\ApplyCouponAction;
use Modules\Core\Helpers\Helper;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Http\Requests\Store as OrderStoreRequest;
use Modules\Order\Models\Order;
use Modules\Shipping\Repository\ShippingRepository;
use Throwable;

class ProcessCheckoutAction
{
    public function __construct(
        private readonly StoreOrderAction $storeOrderAction,
        private readonly ApplyCouponAction $applyCouponAction,
        private readonly ShippingRepository $shippingRepository,
        private readonly MercadoPagoService $mercadoPagoService,
    ) {}

    public function execute(OrderStoreRequest $request): RedirectResponse
    {
        $user      = Auth::user();
        $userId    = (string) ($user?->id ?? '');
        $cartItems = Helper::getAllProductFromCart($userId);
        $subtotal  = Helper::totalCartPrice($userId);
        $quantity  = $cartItems->sum('quantity');

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Seu carrinho está vazio.');
        }

        $shippingId   = null;
        $shippingCost = 0;

        if (Helper::cartRequiresShipping($userId)) {
            $shippingId = $request->input('shipping');
            if ($shippingId) {
                $shipping     = $this->shippingRepository->find((int) $shippingId);
                $shippingCost = $shipping?->price ?? 0;
            }
        }

        $couponData     = session('coupon');
        $couponDiscount = $couponData['discount'] ?? $couponData['value'] ?? 0;
        $couponId       = $couponData['id'] ?? null;

        if (($couponData['free_shipping'] ?? false) && $couponDiscount === 0) {
            $couponDiscount = $shippingCost;
        }

        $paymentMethod = $request->input('payment_method', 'cod');
        $orderData = [
            'user_id'        => $user?->id,
            'sub_total'      => $subtotal,
            'shipping_id'    => $shippingId,
            'total_amount'   => max(0, $subtotal + $shippingCost - $couponDiscount),
            'quantity'       => $quantity,
            'payment_method' => $paymentMethod,
            'payment_status' => 'pending',
            'status'         => 'pending',
            'first_name'     => $request->input('first_name'),
            'last_name'      => $request->input('last_name'),
            'email'          => $request->input('email'),
            'phone'          => $request->input('phone'),
            'country'        => $request->input('country', 'BR'),
            'city'           => $request->input('city'),
            'state'          => $request->input('state'),
            'address1'       => $request->input('address1'),
            'address2'       => $request->input('address2'),
            'post_code'      => $request->input('post_code'),
        ];

        if ($paymentMethod === 'paypal') {
            session()->put('pending_order', $orderData);

            return redirect()->route('payment');
        }

        if ($paymentMethod === 'stripe') {
            session()->put('pending_order', $orderData);

            return redirect()->route('stripe', Auth::id());
        }

        if ($paymentMethod === 'mercadopago') {
            $order = $this->storeOrderAction->execute(OrderDTO::fromArray($orderData));
            $this->associateCartItems($cartItems, $order);
            $this->saveAddressIfRequested($request, $order);
            $this->recordCouponUsage($couponId, $couponDiscount, $order, $user?->id);

            try {
                $preference = $this->mercadoPagoService->createPreference($order);

                return redirect()->away((string) $preference['init_point']);
            } catch (Throwable $exception) {
                report($exception);
                $this->associateCartItems($cartItems, null);
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'unpaid',
                    'mercadopago_status' => 'preference_failed',
                ]);

                return redirect()->back()->withInput()->with(
                    'error',
                    'Não foi possível iniciar o pagamento pelo Mercado Pago. Confira as credenciais no administrativo.'
                );
            }
        }

        $order = $this->storeOrderAction->execute(OrderDTO::fromArray($orderData));

        $this->associateCartItems($cartItems, $order);
        $this->saveAddressIfRequested($request, $order);
        $this->recordCouponUsage($couponId, $couponDiscount, $order, $user?->id);

        session()->forget(['cart', 'coupon', 'pending_order']);

        return redirect()->route('front.index')->with(
            'success',
            'Pedido realizado com sucesso! Número do pedido: '.$order->order_number
        );
    }

    private function associateCartItems(iterable $cartItems, ?Order $order): void
    {
        foreach ($cartItems as $cartItem) {
            $cartItem->update(['order_id' => $order?->id]);
        }
    }

    private function saveAddressIfRequested(OrderStoreRequest $request, Order $order): void
    {
        $user = $order->user;

        if (! $user || ! $request->has('save_address')) {
            return;
        }

        $user->addresses()->create([
            'type'       => 'shipping',
            'is_default' => $request->has('make_default_address'),
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'email'      => $request->input('email'),
            'phone'      => $request->input('phone'),
            'country'    => $request->input('country', 'BR'),
            'city'       => $request->input('city'),
            'address1'   => $request->input('address1'),
            'address2'   => $request->input('address2'),
            'post_code'  => $request->input('post_code'),
        ]);
    }

    private function recordCouponUsage(?int $couponId, float|int $couponDiscount, Order $order, ?int $userId): void
    {
        if (! $couponId) {
            return;
        }

        $this->applyCouponAction->recordUsage(
            $couponId,
            $order->id,
            $userId,
            session()->getId(),
            $couponDiscount
        );
    }
}
