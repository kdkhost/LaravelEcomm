@extends($themePath . '.layouts.master')

@section('title', 'Meus pedidos')

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
    @endphp

    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Meus pedidos</h2>

                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Data</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Pagamento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge badge-{{
                                                $order->status == 'pending' ? 'warning' :
                                                ($order->status == 'processing' ? 'info' :
                                                ($order->status == 'shipped' ? 'primary' :
                                                ($order->status == 'delivered' ? 'success' : 'danger')))
                                            }}">
                                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{
                                                $order->payment_status == 'paid' ? 'success' :
                                                ($order->payment_status == 'pending' ? 'warning' : 'danger')
                                            }}">
                                                {{ $paymentLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-sm btn-primary">Ver</a>
                                            <a href="{{ route('user.orders.track', $order) }}" class="btn btn-sm btn-info">Rastrear</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $orders->links('pagination::admin-bootstrap-5') }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <p>Você ainda não realizou pedidos.</p>
                        <a href="{{ url('/'.app()->getLocale()) }}" class="btn btn-primary">Começar compra</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
