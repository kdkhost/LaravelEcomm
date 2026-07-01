@php
    $themePath = 'front::themes.modern';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Rastrear pedido ' . ($order->order_number ?? $order->id))

@section('content')
    @php
        $statusLabels = [
            'pending' => ['label' => 'Pendente', 'class' => 'warning'],
            'processing' => ['label' => 'Em processamento', 'class' => 'info'],
            'shipped' => ['label' => 'Enviado', 'class' => 'primary'],
            'delivered' => ['label' => 'Entregue', 'class' => 'success'],
            'cancelled' => ['label' => 'Cancelado', 'class' => 'danger'],
        ];
        $status = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'default'];
    @endphp

    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li><a href="{{ route('user.orders.history') }}">Meus pedidos</a></li>
                <li class="active">Rastrear pedido {{ $order->order_number ?? '#'.$order->id }}</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            <div class="row">
                <div class="main col-md-12">
                    <h1 class="page-title">Rastreamento</h1>
                    <div class="separator-2"></div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                Pedido {{ $order->order_number ?? '#'.$order->id }}
                                <span class="pull-right">
                                    <span class="label label-{{ $status['class'] }}">{{ $status['label'] }}</span>
                                </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Informacoes do pedido</h4>
                                    <p><strong>Data:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                                    <p><strong>Total:</strong> R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</p>
                                    <p><strong>Frete:</strong> {{ $order->shipping->type ?? 'Nao informado' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h4>Rastreamento</h4>
                                    <p><strong>Transportadora:</strong> {{ $order->tracking_carrier ?: '-' }}</p>
                                    <p><strong>Codigo:</strong> {{ $order->tracking_number ?: '-' }}</p>
                                    <p><strong>Enviado em:</strong> {{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : '-' }}</p>
                                </div>
                            </div>

                            <div class="space-bottom"></div>

                            <h4>Linha do tempo</h4>
                            <div class="timeline">
                                <div class="timeline-item active">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6>Pedido recebido</h6>
                                        <p>{{ $order->created_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="timeline-item {{ in_array($order->status, ['processing', 'shipped', 'delivered'], true) ? 'active' : '' }}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6>Em processamento</h6>
                                        <p>Pedido em separacao.</p>
                                    </div>
                                </div>
                                <div class="timeline-item {{ in_array($order->status, ['shipped', 'delivered'], true) ? 'active' : '' }}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6>Enviado</h6>
                                        <p>Pedido entregue a transportadora.</p>
                                    </div>
                                </div>
                                <div class="timeline-item {{ $order->status === 'delivered' ? 'active' : '' }}">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6>Entregue</h6>
                                        <p>Pedido finalizado.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-bottom"></div>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('user.orders.history') }}" class="btn btn-default">
                                        <i class="icon-left-open-big"></i> Voltar aos pedidos
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-default">
                                        <i class="fa fa-eye"></i> Ver detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .timeline { position: relative; padding: 20px 0; }
        .timeline-item { position: relative; padding-left: 40px; margin-bottom: 30px; }
        .timeline-item::before { content: ''; position: absolute; left: 10px; top: 0; bottom: -30px; width: 2px; background: #ddd; }
        .timeline-item:last-child::before { display: none; }
        .timeline-item.active::before { background: #28a745; }
        .timeline-marker { position: absolute; left: 0; top: 0; width: 20px; height: 20px; border-radius: 50%; background: #ddd; border: 3px solid #fff; }
        .timeline-item.active .timeline-marker { background: #28a745; }
        .timeline-content h6 { margin: 0 0 5px; font-weight: bold; }
        .timeline-content p { margin: 0; color: #666; }
    </style>
@endpush
