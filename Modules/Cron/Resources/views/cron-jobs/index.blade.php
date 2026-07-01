@extends('admin::layouts.master')
@section('title', 'E-SHOP || ' . __('cron::messages.title'))
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">@lang('cron::messages.list')</h6>
            <div>
                <a href="{{ route('admin.cron-jobs.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> @lang('cron::messages.add_new')
                </a>
                <a href="{{ route('admin.cron.logs') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-history"></i> @lang('cron::messages.logs')
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3">
                        <span class="font-weight-bold mr-2">@lang('cron::messages.worker_status'):</span>
                        <span id="workerStatusBadge" class="badge badge-secondary">@lang('cron::messages.stopped')</span>
                        <button id="startWorkerBtn" class="btn btn-success btn-sm ml-2">
                            <i class="fas fa-play"></i> @lang('cron::messages.start_worker')
                        </button>
                        <button id="stopWorkerBtn" class="btn btn-danger btn-sm ml-1">
                            <i class="fas fa-stop"></i> @lang('cron::messages.stop_worker')
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                @if(count($jobs) > 0)
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('cron::messages.name')</th>
                            <th>@lang('cron::messages.command')</th>
                            <th>@lang('cron::messages.frequency')</th>
                            <th>@lang('cron::messages.status')</th>
                            <th>@lang('cron::messages.last_run')</th>
                            <th>@lang('cron::messages.next_run')</th>
                            <th>@lang('cron::messages.is_active')</th>
                            <th>@lang('cron::messages.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($jobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ $job->name }}</td>
                                <td><code>{{ $job->command }}</code></td>
                                <td>@lang('cron::messages.' . $job->frequency)</td>
                                <td>
                                    @if($job->status === 'success' || $job->status === 'idle')
                                        <span class="badge badge-success">{{ $job->status }}</span>
                                    @elseif($job->status === 'running')
                                        <span class="badge badge-primary">{{ $job->status }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ $job->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $job->last_run_at ? $job->last_run_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>{{ $job->next_run_at ? $job->next_run_at->format('d/m/Y H:i:s') : '-' }}</td>
                                <td>
                                    @if($job->is_active)
                                        <span class="badge badge-success">@lang('partials.yes')</span>
                                    @else
                                        <span class="badge badge-warning">@lang('partials.no')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.cron-jobs.edit', $job->id) }}"
                                       class="btn btn-primary btn-sm" style="height:30px;width:30px;border-radius:50%"
                                       data-toggle="tooltip" title="@lang('partials.edit')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.cron-jobs.run', $job->id) }}"
                                       class="btn btn-success btn-sm" style="height:30px;width:30px;border-radius:50%"
                                       data-toggle="tooltip" title="@lang('cron::messages.run_now')">
                                        <i class="fas fa-play"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.cron-jobs.destroy', $job->id) }}"
                                          style="display:inline">
                                        @csrf @method('delete')
                                        <button class="btn btn-danger btn-sm" style="height:30px;width:30px;border-radius:50%"
                                                data-toggle="tooltip" title="@lang('partials.delete')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">@lang('cron::messages.no_records')</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
function checkWorkerStatus() {
    fetch('{{ route('admin.cron.worker.status') }}')
        .then(r => r.json())
        .then(d => {
            const badge = document.getElementById('workerStatusBadge');
            if (d.running) {
                badge.className = 'badge badge-success';
                badge.textContent = '@lang('cron::messages.running')';
            } else {
                badge.className = 'badge badge-secondary';
                badge.textContent = '@lang('cron::messages.stopped')';
            }
        });
}
document.getElementById('startWorkerBtn')?.addEventListener('click', function() {
    fetch('{{ route('admin.cron.worker.start') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(() => setTimeout(checkWorkerStatus, 1000));
});
document.getElementById('stopWorkerBtn')?.addEventListener('click', function() {
    fetch('{{ route('admin.cron.worker.stop') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(() => setTimeout(checkWorkerStatus, 1000));
});
setInterval(checkWorkerStatus, 10000);
checkWorkerStatus();
</script>
@endpush
