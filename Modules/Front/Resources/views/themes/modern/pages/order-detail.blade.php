@php
    $themePath = 'front::themes.modern';
@endphp
@extends($themePath . '.layouts.master')

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
            'refunded' => ['label' => 'Reembolsado', 'class' => 'default'],
        ];
        $paymentMethods = [
            'cod' => 'Pagamento na entrega',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'mercadopago' => 'Mercado Pago',
        ];
        $orderStatus = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'default'];
        $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst((string) $order->payment_status), 'class' => 'default'];
        $shippingCost = (float) (optional($order->shipping)->price ?? 0);
    @endphp

    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li><a href="{{ route('front.my-orders') }}">Meus pedidos</a></li>
                <li class="active">Pedido {{ $order->order_number ?? '#'.$order->id }}</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            <div class="row">
                <div class="main col-md-12">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Pedido {{ $order->order_number ?? '#'.$order->id }}
                                <span class="pull-right">
                                    <span class="label label-{{ $orderStatus['class'] }}">{{ $orderStatus['label'] }}</span>
                                    <span class="label label-{{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span>
                                </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Informacoes do pedido</h4>
                                    <table class="table table-condensed">
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
                                    <h4>Endereco de entrega</h4>
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

                            <h4>Itens do pedido</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
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

                            <div class="space-bottom"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('front.my-orders') }}" class="btn btn-default">
                                        <i class="icon-left-open-big"></i> Voltar aos pedidos
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <form action="{{ route('front.reorder', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-default">
                                            <i class="fa fa-refresh"></i> Comprar novamente
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
