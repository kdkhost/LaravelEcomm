@extends('front::layouts.master')

@section('title', 'Ofertas')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ url('/') }}">@lang('frontend.home')<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="#">Ofertas</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style -->
    <section class="product-area shop-sidebar shop section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="shop-sidebar">
                        <!-- Recent Products -->
                        <div class="single-widget recent">
                            <h3 class="title">@lang('frontend.recent_products')</h3>
                            @foreach ($recent_products as $product)
                                <div class="single-post">
                                    <div class="image">
                                        <img src="{{ $product->photo ?? asset('placeholder/image?type=product&text=' . urlencode($product->title) . '&index=' . $product->id) }}" alt="{{ $product->title }}">
                                    </div>
                                    <div class="content">
                                        <h5><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h5>
                                        <p class="price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!--/ End Recent Products -->

                        <!-- Brands -->
                        <div class="single-widget category">
                            <h3 class="title">@lang('frontend.brands')</h3>
                            <ul class="categor-list">
                                @foreach ($brands as $brand)
                                    <li>
                                        <a href="{{ route('front.product-brand', $brand->slug) }}">{{ $brand->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!--/ End Brands -->
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="shop-top">
                                <div class="shop-shorter">
                                    <div class="single-shorter">
                                        <h4 class="title">Ofertas Especiais</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="single-product">
                                    <div class="product-img">
                                        <a href="{{ route('front.product-detail', $product->slug) }}">
                                            <img class="default-img" src="{{ $product->photo ?? asset('placeholder/image?type=product&text=' . urlencode($product->title) . '&index=' . $product->id) }}" alt="{{ $product->title }}">
                                            <img class="hover-img" src="{{ $product->photo ?? asset('placeholder/image?type=product&text=' . urlencode($product->title) . '&index=' . $product->id) }}" alt="{{ $product->title }}">
                                        </a>
                                        <div class="button-head">
                                            <div class="product-action">
                                                <a data-toggle="modal" data-target="#{{ $product->id }}" title="Quick View" href="#"><i class="ti-eye"></i><span>@lang('frontend.quick_shop')</span></a>
                                                <a title="Wishlist" href="{{ route('add-to-wishlist', $product->slug) }}"><i class="ti-heart"></i><span>@lang('frontend.add_to_wishlist')</span></a>
                                            </div>
                                            <div class="product-action-2">
                                                <a href="{{ route('front.add-to-cart', $product->slug) }}">@lang('frontend.add_to_cart')</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                        <div class="product-price">
                                            @if ($product->discount)
                                                <span class="old">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                                <span>R$ {{ number_format($product->price - ($product->price * $product->discount / 100), 2, ',', '.') }}</span>
                                            @else
                                                <span>R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Product Style -->
@endsection
