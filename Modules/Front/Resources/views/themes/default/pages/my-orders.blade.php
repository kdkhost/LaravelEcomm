@extends('front::layouts.master')

@section('title', 'Meus pedidos')

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
    @endphp

    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Inicio<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Meus pedidos</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="my-orders section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Meus pedidos</h2>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Pagamento</th>
                                    <th>Acoes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    @php
                                        $orderStatus = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'secondary'];
                                        $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst((string) $order->payment_status), 'class' => 'secondary'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('front.order-detail', $order) }}">
                                                {{ $order->order_number ?? '#'.$order->id }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $orderStatus['class'] }}">{{ $orderStatus['label'] }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('front.order-detail', $order) }}" class="btn btn-sm btn-info">
                                                Ver pedido
                                            </a>
                                            <form action="{{ route('front.reorder', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    Comprar novamente
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $orders->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="ti-shopping-cart-full fa-3x text-muted mb-3"></i>
                            <h5>Nenhum pedido encontrado</h5>
                            <p class="text-muted">Voce ainda nao realizou compras nesta loja.</p>
                            <a href="{{ route('front.product-grids') }}" class="btn btn-primary">
                                Comecar compra
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
