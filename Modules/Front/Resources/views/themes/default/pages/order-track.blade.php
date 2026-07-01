@extends('front::layouts.master')

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
        $status = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'secondary'];
    @endphp

    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Inicio<i class="ti-arrow-right"></i></a></li>
                            <li><a href="{{ route('user.orders.history') }}">Meus pedidos<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Rastrear pedido</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="tracking_box_area section_gap py-5">
        <div class="container">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Pedido {{ $order->order_number ?? '#'.$order->id }}</h4>
                    <span class="badge badge-{{ $status['class'] }}">{{ $status['label'] }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informacoes do pedido</h5>
                            <p><strong>Data:</strong> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
                            <p><strong>Total:</strong> R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</p>
                            <p><strong>Frete:</strong> {{ $order->shipping->type ?? 'Nao informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Rastreamento</h5>
                            <p><strong>Transportadora:</strong> {{ $order->tracking_carrier ?: '-' }}</p>
                            <p><strong>Codigo:</strong> {{ $order->tracking_number ?: '-' }}</p>
                            <p><strong>Enviado em:</strong> {{ $order->shipped_at ? $order->shipped_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                    </div>

                    <h5 class="mb-3">Linha do tempo</h5>
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

                    <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-primary">Ver detalhes do pedido</a>
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
