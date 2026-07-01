@extends('admin::layouts.master')

@section('title', 'Detalhes do pedido')

@section('content')
    @php
        $statusLabels = [
            'pending' => ['label' => 'Pendente', 'class' => 'warning'],
            'processing' => ['label' => 'Em processamento', 'class' => 'info'],
            'shipped' => ['label' => 'Enviado', 'class' => 'primary'],
            'delivered' => ['label' => 'Entregue', 'class' => 'success'],
            'cancelled' => ['label' => 'Cancelado', 'class' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'class' => 'secondary'],
        ];
        $paymentStatusLabels = [
            'pending' => ['label' => 'Pendente', 'class' => 'warning'],
            'paid' => ['label' => 'Pago', 'class' => 'success'],
            'unpaid' => ['label' => 'Nao pago', 'class' => 'danger'],
            'refunded' => ['label' => 'Reembolsado', 'class' => 'secondary'],
            'partially_refunded' => ['label' => 'Parcialmente reembolsado', 'class' => 'secondary'],
        ];
        $paymentMethods = [
            'cod' => 'Pagamento na entrega',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'mercadopago' => 'Mercado Pago',
            'bank_transfer' => 'Transferencia bancaria',
            'credit_card' => 'Cartao de credito',
        ];
        $orderStatus = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'secondary'];
        $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ['label' => ucfirst((string) $order->payment_status), 'class' => 'secondary'];
        $customerName = trim(($order->user->name ?? '') ?: trim(($order->first_name ?? '').' '.($order->last_name ?? '')));
        $customerEmail = $order->user->email ?? $order->email ?? '-';
        $shippingCost = (float) (optional($order->shipping)->price ?? 0);
    @endphp

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Pedido {{ $order->order_number ?? '#'.$order->id }}</h5>
            <div>
                <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <a href="{{ route('order.pdf', $order->id) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-download"></i> PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-uppercase text-muted mb-3">Resumo</h6>
                        <p class="mb-1"><strong>Data:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                        <p class="mb-1">
                            <strong>Status:</strong>
                            <span class="badge badge-{{ $orderStatus['class'] }}">{{ $orderStatus['label'] }}</span>
                        </p>
                        <p class="mb-1">
                            <strong>Pagamento:</strong>
                            <span class="badge badge-{{ $paymentStatus['class'] }}">{{ $paymentStatus['label'] }}</span>
                        </p>
                        <p class="mb-0"><strong>Total:</strong> R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-uppercase text-muted mb-3">Cliente</h6>
                        <p class="mb-1"><strong>Nome:</strong> {{ $customerName ?: 'Cliente nao informado' }}</p>
                        <p class="mb-1"><strong>E-mail:</strong> {{ $customerEmail }}</p>
                        <p class="mb-0"><strong>Telefone:</strong> {{ $order->phone ?: '-' }}</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-uppercase text-muted mb-3">Pagamento</h6>
                        <p class="mb-1"><strong>Metodo:</strong> {{ $paymentMethods[$order->payment_method] ?? ucfirst((string) $order->payment_method) }}</p>
                        <p class="mb-1"><strong>Transacao:</strong> {{ $order->transaction_reference ?: '-' }}</p>
                        <p class="mb-0"><strong>Mercado Pago:</strong> {{ $order->mercadopago_payment_id ?: $order->mercadopago_preference_id ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-uppercase text-muted mb-3">Entrega</h6>
                        <p class="mb-1">{{ $order->address1 ?: '-' }}</p>
                        @if($order->address2)
                            <p class="mb-1">{{ $order->address2 }}</p>
                        @endif
                        <p class="mb-1">{{ $order->city ?: '-' }} / {{ $order->state ?: '-' }}</p>
                        <p class="mb-1">CEP {{ $order->post_code ?: '-' }}</p>
                        <p class="mb-0">{{ $order->country ?: 'BR' }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-uppercase text-muted mb-3">Rastreamento</h6>
                        <p class="mb-1"><strong>Transportadora:</strong> {{ $order->tracking_carrier ?: '-' }}</p>
                        <p class="mb-1"><strong>Codigo:</strong> {{ $order->tracking_number ?: '-' }}</p>
                        <p class="mb-0"><strong>Enviado em:</strong> {{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-right">Preco</th>
                        <th class="text-right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($order->carts as $item)
                        <tr>
                            <td>{{ $item->product->title ?? 'Produto removido' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">R$ {{ number_format((float) $item->price, 2, ',', '.') }}</td>
                            <td class="text-right">R$ {{ number_format((float) $item->amount, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Nenhum item vinculado a este pedido.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Subtotal</th>
                        <th class="text-right">R$ {{ number_format((float) $order->sub_total, 2, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Frete</th>
                        <th class="text-right">R$ {{ number_format($shippingCost, 2, ',', '.') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Total</th>
                        <th class="text-right">R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
