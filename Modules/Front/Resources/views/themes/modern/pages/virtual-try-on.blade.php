@php
    $themePath = 'front::themes.modern';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Provador virtual 360')

@section('content')
    <section class="main-container">
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
