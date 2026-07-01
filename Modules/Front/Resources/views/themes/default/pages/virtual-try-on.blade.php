@php
    $themePath = 'front::themes.default';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Provador virtual 360')

@section('content')
    <section class="shop single section">
        <div class="container">
            @include('front::partials.virtual-try-on-studio', [
                'product' => $product,
                'productImages' => $productImages,
                'products' => $products,
                'sizes' => $sizes,
            ])
        </div>
    </section>
@endsection
