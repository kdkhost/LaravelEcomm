@extends('admin::layouts.master')
@section('title', 'E-SHOP || ' . __('cron::messages.logs'))
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('cron::messages.logs')</h6>
            <a href="{{ route('admin.cron-jobs.index') }}" class="btn btn-primary btn-sm float-right">
                <i class="fas fa-arrow-left"></i> @lang('partials.back')
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($logs) > 0)
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('cron::messages.name')</th>
                            <th>@lang('cron::messages.status')</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Duração (s)</th>
                            <th>@lang('cron::messages.command')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->job?->name ?? '-' }}</td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="badge badge-success">{{ $log->status }}</span>
                                    @elseif($log->status === 'running')
                                        <span class="badge badge-primary">{{ $log->status }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->started_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                                <td>{{ $log->finished_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                                <td>{{ $log->duration ? number_format($log->duration, 2) : '-' }}</td>
                                <td>
                                    @if($log->output)
                                        <pre class="mb-0" style="max-height:60px;overflow:auto;font-size:11px">{{ $log->output }}</pre>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">Nenhum log encontrado.</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
