@extends('admin::layouts.master')

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
    @endphp

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Pedidos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(!$orders->isEmpty())
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('sidebar.shipping')</th>
                            <th>@lang('partials.total_amount')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.order_no')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.quantity')</th>
                            <th>@lang('sidebar.shipping')</th>
                            <th>@lang('partials.total_amount')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($orders as $order)
                            @php
                                $customerName = trim(($order->user->name ?? '') ?: trim(($order->first_name ?? '').' '.($order->last_name ?? '')));
                                $customerEmail = $order->user->email ?? $order->email ?? '-';
                                $status = $statusLabels[$order->status] ?? ['label' => ucfirst((string) $order->status), 'class' => 'secondary'];
                            @endphp
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $customerName ?: 'Cliente nao informado' }}</td>
                                <td>{{ $customerEmail }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ $order->shipping->type ?? 'Nao informado' }}</td>
                                <td>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-{{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                                <td>
                                    <a href="{{route('orders.show',$order->id)}}"
                                       class="btn btn-warning btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="Visualizar" data-placement="bottom"><i class="fas fa-eye"></i></a>
                                    <a href="{{route('orders.edit',$order->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="Editar" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('admin.complaints.create', $order->id) }}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="Registrar atendimento" data-placement="bottom"><i class="fas fa-comment"></i></a>
                                    <form method="POST" action="{{route('orders.destroy',[$order->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$order->id}}" style="height:30px; width:30px;
                                                border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Excluir"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">@lang('partials.no_records_found')</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
