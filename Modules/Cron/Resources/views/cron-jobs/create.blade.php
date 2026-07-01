@extends('admin::layouts.master')
@section('title', 'E-SHOP || ' . __('cron::messages.add_new'))
@section('content')
    <div class="card">
        <h5 class="card-header">@lang('cron::messages.add_new')</h5>
        <div class="card-body">
            @include('cron::partials.form')
        </div>
    </div>
@endsection
