@extends('front::layouts.master')

@section('title', 'Pedido ' . ($order->order_number ?? $order->id))

@section('content')
    @php
        $statusLabels = [
            'pending' => ['label' => 'Pendente', 'class' => 'warning'],
            'processing' => ['label' => 'Em processamento', 'class' => 'info'],
            'shipped' => ['label' => 'Enviado', 'class' => 'primary'],
            'delivered' => ['label' => 'Entregue', 'class' => 'success'],
            'cancelled' => ['label' => 'Cancelado', 'class' => 'danger'],
        ];
        $paymentStatusLabels = [
            'pending' => ['label' => 'Pendente', 'class' => 'warning'],
            'paid' => ['label' => 'Pago', 'class' => 'success'],
            'unpaid' => ['label' => 'Nao pago', 'class' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'class' => 'secondary'],
        ];
        $paymentMethods = [
            'cod' => 'Pagamento na entrega',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'mercadopago' => 'Mercado Pago',
        ];
        $orderStatus = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'secondary'];
        $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst((string) $order->payment_status), 'class' => 'secondary'];
        $shippingCost = (float) (optional($order->shipping)->price ?? 0);
    @endphp

    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Inicio<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('front.my-orders') }}">Meus pedidos<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Pedido {{ $order->order_number ?? '#'.$order->id }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="order-detail section">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Pedido {{ $order->order_number ?? '#'.$order->id }}</h4>
                    <div>
                        <span class="badge badge-{{ $orderStatus['class'] }} mr-2">{{ $orderStatus['label'] }}</span>
                        <span class="badge badge-{{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informacoes do pedido</h6>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Data:</strong></td>
                                    <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pagamento:</strong></td>
                                    <td>{{ $paymentMethods[$order->payment_method] ?? ucfirst((string) $order->payment_method) }}</td>
                                </tr>
                                @if($order->transaction_reference)
                                    <tr>
                                        <td><strong>Transacao:</strong></td>
                                        <td>{{ $order->transaction_reference }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Endereco de entrega</h6>
                            <address>
                                <strong>{{ trim(($order->first_name ?? '').' '.($order->last_name ?? '')) ?: auth()->user()?->name }}</strong><br>
                                {{ $order->address1 ?: '-' }}<br>
                                @if($order->address2)
                                    {{ $order->address2 }}<br>
                                @endif
                                {{ $order->city ?: '-' }} / {{ $order->state ?: '-' }} - CEP {{ $order->post_code ?: '-' }}<br>
                                {{ $order->country ?: 'BR' }}<br>
                                @if($order->phone)
                                    <abbr title="Telefone">Tel.:</abbr> {{ $order->phone }}
                                @endif
                            </address>
                        </div>
                    </div>

                    <hr>

                    <h6>Itens do pedido</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>Produto</th>
                                <th>Preco</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->carts as $item)
                                <tr>
                                    <td>
                                        @if($item->product)
                                            <a href="{{ route('front.product-detail', $item->product->slug) }}">
                                                {{ $item->product->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Produto indisponivel</span>
                                        @endif
                                    </td>
                                    <td>R$ {{ number_format((float) $item->price, 2, ',', '.') }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>R$ {{ number_format((float) $item->amount, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Nenhum item encontrado.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                <td>R$ {{ number_format((float) $order->sub_total, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Frete:</strong></td>
                                <td>R$ {{ number_format($shippingCost, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</strong></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('front.my-orders') }}" class="btn btn-secondary">
                            <i class="ti-arrow-left"></i> Voltar aos pedidos
                        </a>
                        <form action="{{ route('front.reorder', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-reload"></i> Comprar novamente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
