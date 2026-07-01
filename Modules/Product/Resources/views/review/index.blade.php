@extends('admin::layouts.master')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($reviews) && $reviews->count() > 0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.review_by')</th>
                            <th>@lang('partials.product_title')</th>
                            <th>@lang('partials.review')</th>
                            <th>@lang('partials.rate')</th>
                            <th>@lang('partials.date')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.review_by')</th>
                            <th>@lang('partials.product_title')</th>
                            <th>@lang('partials.review')</th>
                            <th>@lang('partials.rate')</th>
                            <th>@lang('partials.date')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($reviews as $review)
                            <tr>
                                <td>{{ $review->id }}</td>
                                <td>{{ $review->user->name ?? '' }}</td>
                                <td>{{ $review->product->title ?? '' }}</td>
                                <td>{{ $review->review }}</td>
                                <td>
                                    <ul class="list-unstyled d-flex mb-0">
                                        @for($i = 1; $i <= 5; $i++)
                                            <li class="mr-1 text-warning">
                                                <i class="fa{{ $review->rate >= $i ? ' fa-star' : 'r fa-star' }}"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                </td>
                                <td>{{ $review->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($review->status === 'active')
                                        <span class="badge badge-success">{{ $review->status }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $review->status }}</span>
                                    @endif
                                </td>
                                <td class="d-flex">
                                    <a href="{{ route('reviews.edit', $review->id) }}"
                                       class="btn btn-primary btn-sm mr-1"
                                       style="height:30px; width:30px; border-radius:50%"
                                       data-toggle="tooltip"
                                       title="Editar"
                                       data-placement="bottom">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('reviews.destroy', $review->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{ $review->id }}"
                                                style="height:30px; width:30px; border-radius:50%"
                                                data-toggle="tooltip"
                                                data-placement="bottom"
                                                title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
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
