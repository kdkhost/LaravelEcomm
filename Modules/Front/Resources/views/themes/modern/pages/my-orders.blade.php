@php
    $themePath = 'front::themes.modern';
@endphp
@extends($themePath . '.layouts.master')

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
            'refunded' => ['label' => 'Reembolsado', 'class' => 'default'],
        ];
    @endphp

    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li class="active">Meus pedidos</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            <div class="row">
                <div class="main col-md-12">
                    <h1 class="page-title">Meus pedidos</h1>
                    <div class="separator-2"></div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
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
                                        $orderStatus = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'default'];
                                        $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst((string) $order->payment_status), 'class' => 'default'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('front.order-detail', $order) }}">
                                                {{ $order->order_number ?? '#'.$order->id }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                        <td><span class="label label-{{ $orderStatus['class'] }}">{{ $orderStatus['label'] }}</span></td>
                                        <td><span class="label label-{{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span></td>
                                        <td>
                                            <a href="{{ route('front.order-detail', $order) }}" class="btn btn-sm btn-default">
                                                <i class="fa fa-eye"></i> Ver pedido
                                            </a>
                                            <form action="{{ route('front.reorder', $order) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-default">
                                                    <i class="fa fa-refresh"></i> Comprar novamente
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fa fa-shopping-cart fa-3x mb-3"></i>
                            <h4>Nenhum pedido encontrado</h4>
                            <p>Voce ainda nao realizou compras nesta loja.</p>
                            <a href="{{ route('front.product-grids') }}" class="btn btn-default">
                                Comecar compra
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
