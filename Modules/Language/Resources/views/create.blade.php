@extends('admin::layouts.master')

@section('title','E-SHOP || ' . __('language::messages.add_new'))

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('language::messages.add_new')</h5>
        <div class="card-body">
            @include('language::partials.form')
        </div>
    </div>
@endsection
