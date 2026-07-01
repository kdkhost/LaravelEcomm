@extends('admin::layouts.master')

@section('title', 'Editar avaliacao')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('partials.edit')</h5>
        <div class="card-body">
            <form action="{{ route('reviews.update', $review->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="name">@lang('partials.review_by'):</label>
                    <input type="text" disabled class="form-control" value="{{ $review->user->name ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="review">@lang('partials.review')</label>
                    <textarea name="review" cols="20" rows="10" class="form-control">{{ $review->review }}</textarea>
                </div>
                <div class="form-group">
                    <label for="status">@lang('partials.status') :</label>
                    <select name="status" class="form-control">
                        <option value="">-- Selecione o status --</option>
                        <option value="active" @selected($review->status === 'active')>Active</option>
                        <option value="inactive" @selected($review->status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-info, .shipping-info {
            background: #ececec;
            padding: 20px;
        }

        .order-info h4, .shipping-info h4 {
            text-decoration: underline;
        }
    </style>
@endpush
