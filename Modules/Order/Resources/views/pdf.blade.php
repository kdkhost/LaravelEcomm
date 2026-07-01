<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Pedido {{ $order?->order_number ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
        .header { border-bottom: 2px solid #198754; padding-bottom: 12px; margin-bottom: 18px; }
        .brand { float: left; }
        .company { float: right; text-align: right; }
        .clear { clear: both; }
        .box { border: 1px solid #ddd; padding: 12px; margin-bottom: 14px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #198754; color: #fff; text-align: left; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total { font-size: 15px; font-weight: bold; }
        .signature { margin-top: 46px; text-align: right; }
    </style>
</head>
<body>
@if($order)
    @php
        $items = $order->cart_info ?? $order->carts ?? collect();
        $shippingCost = (float) (optional($order->shipping)->price ?? 0);
        $customerName = trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: ($order->user->name ?? 'Cliente');
        $customerEmail = $order->email ?: ($order->user->email ?? '-');
        $paymentMethods = [
            'cod' => 'Pagamento na entrega',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'mercadopago' => 'Mercado Pago',
        ];
    @endphp

    <div class="header">
        <div class="brand">
            <h2>{{ config('app.name') }}</h2>
            <div class="muted">Comprovante de pedido</div>
        </div>
        <div class="company">
            <strong>{{ env('APP_NAME', config('app.name')) }}</strong><br>
            {{ env('APP_ADDRESS') }}<br>
            Tel.: {{ env('APP_PHONE') }}<br>
            E-mail: {{ env('APP_EMAIL') }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="box">
        <strong>Pedido:</strong> {{ $order->order_number ?? '#'.$order->id }}<br>
        <strong>Data:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}<br>
        <strong>Forma de pagamento:</strong> {{ $paymentMethods[$order->payment_method] ?? ucfirst((string) $order->payment_method) }}<br>
        <strong>Status do pagamento:</strong> {{ $order->payment_status }}
    </div>

    <div class="box">
        <strong>Cliente</strong><br>
        {{ $customerName }}<br>
        {{ $customerEmail }}<br>
        {{ $order->phone ?: '-' }}<br><br>
        <strong>Entrega</strong><br>
        {{ $order->address1 ?: '-' }}
        @if($order->address2)
            , {{ $order->address2 }}
        @endif
        <br>
        {{ $order->city ?: '-' }} / {{ $order->state ?: '-' }} - CEP {{ $order->post_code ?: '-' }}<br>
        {{ $order->country ?: 'BR' }}
    </div>

    <table>
        <thead>
        <tr>
            <th>Produto</th>
            <th class="center">Qtd.</th>
            <th class="right">Preco</th>
            <th class="right">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $cart)
            <tr>
                <td>{{ $cart->product->title ?? 'Produto removido' }}</td>
                <td class="center">{{ $cart->quantity }}</td>
                <td class="right">R$ {{ number_format((float) $cart->price, 2, ',', '.') }}</td>
                <td class="right">R$ {{ number_format((float) $cart->amount, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="center muted">Nenhum item vinculado a este pedido.</td>
            </tr>
        @endforelse
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="right">Subtotal</td>
            <td class="right">R$ {{ number_format((float) $order->sub_total, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right">Frete</td>
            <td class="right">R$ {{ number_format($shippingCost, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right total">Total</td>
            <td class="right total">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
        </tr>
        </tfoot>
    </table>

    <p>Obrigado pela compra.</p>
    <div class="signature">
        _______________________________<br>
        Assinatura autorizada
    </div>
@else
    <h5>Pedido invalido</h5>
@endif
</body>
</html>
