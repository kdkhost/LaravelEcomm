@php $job ??= new \Modules\Cron\Models\CronJob; @endphp
<form class="form-horizontal" method="POST"
      action="{{ route($job->exists ? 'admin.cron-jobs.update' : 'admin.cron-jobs.store', $job->exists ? $job->id : null) }}">
    @csrf
    @if($job->exists) @method('put') @endif

    <div class="form-group">
        <label class="col-form-label">@lang('cron::messages.name') <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $job->name ?? '') }}" placeholder="Ex: Limpar Cache">
    </div>
    <div class="form-group">
        <label class="col-form-label">@lang('cron::messages.command') <span class="text-danger">*</span></label>
        <input type="text" name="command" class="form-control"
               value="{{ old('command', $job->command ?? '') }}" placeholder="Ex: cache:clear">
        <small class="form-text text-muted">Nome do comando Artisan (ex: cache:clear, queue:work)</small>
    </div>
    <div class="form-group">
        <label class="col-form-label">@lang('cron::messages.params')</label>
        <input type="text" name="params" class="form-control"
               value="{{ old('params', is_array($job->params ?? null) ? json_encode($job->params) : ($job->params ?? '')) }}"
               placeholder='{"--force": true}'>
        <small class="form-text text-muted">Parâmetros JSON (opcional)</small>
    </div>
    <div class="form-group">
        <label class="col-form-label">@lang('cron::messages.frequency') <span class="text-danger">*</span></label>
        <select name="frequency" class="form-control">
            @foreach(['everyMinute','everyFiveMinutes','everyTenMinutes','everyFifteenMinutes','everyThirtyMinutes','hourly','everyTwoHours','everyThreeHours','everyFourHours','everySixHours','daily','weekly'] as $freq)
                <option value="{{ $freq }}" {{ old('frequency', $job->frequency ?? 'hourly') === $freq ? 'selected' : '' }}>
                    @lang('cron::messages.' . $freq)
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                {{ old('is_active', $job->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">@lang('cron::messages.is_active')</label>
        </div>
    </div>
    <div class="button-container">
        <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
        <button class="btn btn-success" type="submit">@lang('cron::messages.save')</button>
    </div>
</form>
