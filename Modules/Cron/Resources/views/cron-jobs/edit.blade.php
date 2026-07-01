@extends('admin::layouts.master')
@section('title', 'E-SHOP || ' . __('cron::messages.edit'))
@section('content')
    <div class="card">
        <h5 class="card-header">@lang('cron::messages.edit')</h5>
        <div class="card-body">
            @include('cron::partials.form')
        </div>
    </div>
@endsection
