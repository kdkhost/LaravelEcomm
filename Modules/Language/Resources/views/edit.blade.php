@extends('admin::layouts.master')

@section('title','E-SHOP || ' . __('language::messages.edit'))

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('language::messages.edit')</h5>
        <div class="card-body">
            @include('language::partials.form')
        </div>
    </div>
@endsection
