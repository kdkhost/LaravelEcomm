@extends($themePath . '.layouts.master')

@section('title', 'Detalhes do pedido')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Pendente',
            'processing' => 'Em separação',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado',
        ];
        $paymentLabels = [
            'pending' => 'Aguardando pagamento',
            'paid' => 'Pago',
            'unpaid' => 'Não pago',
        ];
        $paymentMethods = [
            'cod' => 'Pagamento na entrega',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'mercadopago' => 'Mercado Pago',
        ];
    @endphp

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Detalhes do pedido</h2>

                <div class="card">
                    <div class="card-header">
                        <h5>Pedido {{ $order->order_number }}</h5>
                        <a href="{{ route('user.orders.history') }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Informações do pedido</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Número:</strong></td>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Data:</strong></td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pagamento:</strong></td>
                                        <td>{{ $paymentLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Forma:</strong></td>
                                        <td>{{ $paymentMethods[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                    </tr>
                                    @if($order->transaction_reference)
                                        <tr>
                                            <td><strong>Referência:</strong></td>
                                            <td>{{ $order->transaction_reference }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>Entrega</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Nome:</strong></td>
                                        <td>{{ trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: ($order->user->name ?? 'Cliente') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>E-mail:</strong></td>
                                        <td>{{ $order->email ?: ($order->user->email ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Endereço:</strong></td>
                                        <td>{{ $order->address1 }} {{ $order->address2 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cidade/UF:</strong></td>
                                        <td>{{ $order->city }} {{ $order->state }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <h5 class="mb-3">Itens</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->carts as $cart)
                                        <tr>
                                            <td>{{ $cart->product?->title ?? 'Produto #'.$cart->product_id }}</td>
                                            <td>{{ $cart->quantity }}</td>
                                            <td>R$ {{ number_format($cart->price, 2, ',', '.') }}</td>
                                            <td>R$ {{ number_format($cart->quantity * $cart->price, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td><strong>R$ {{ number_format($order->sub_total, 2, ',', '.') }}</strong></td>
                                    </tr>
                                    @if($order->shipping)
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Frete:</strong></td>
                                            <td><strong>R$ {{ number_format($order->shipping->price ?? 0, 2, ',', '.') }}</strong></td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td><strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <a href="{{ route('user.orders.track', $order) }}" class="btn btn-primary">Rastrear pedido</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
