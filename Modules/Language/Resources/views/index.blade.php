@extends('admin::layouts.master')
@section('title','E-SHOP || ' . __('language::messages.title'))
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
            <a href="{{route('admin.languages.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="@lang('language::messages.add_new')"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($languages)>0)
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('language::messages.code')</th>
                            <th>@lang('language::messages.name')</th>
                            <th>@lang('language::messages.native_name')</th>
                            <th>@lang('language::messages.flag')</th>
                            <th>@lang('language::messages.direction')</th>
                            <th>@lang('language::messages.is_active')</th>
                            <th>@lang('language::messages.is_default')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($languages as $language)
                            <tr>
                                <td>{{$language->id}}</td>
                                <td>{{$language->code}}</td>
                                <td>{{$language->name}}</td>
                                <td>{{$language->native_name}}</td>
                                <td>{{$language->flag ?? '🌐'}}</td>
                                <td>{{$language->direction === 'rtl' ? __('language::messages.rtl') : __('language::messages.ltr')}}</td>
                                <td>
                                    @if($language->is_active)
                                        <span class="badge badge-success">@lang('partials.yes')</span>
                                    @else
                                        <span class="badge badge-warning">@lang('partials.no')</span>
                                    @endif
                                </td>
                                <td>
                                    @if($language->is_default)
                                        <span class="badge badge-primary">@lang('partials.yes')</span>
                                    @else
                                        <span class="badge badge-secondary">@lang('partials.no')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('admin.languages.edit',$language->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="@lang('partials.edit')" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('admin.languages.destroy',$language->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$language->id}}" style="height:30px; width:30px;border-radius:50%"
                                                data-toggle="tooltip" data-placement="bottom" title="@lang('partials.delete')"><i
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
