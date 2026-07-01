@extends('front::layouts.master')

@section('title','Recently Viewed Products')

@section('content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Início<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Recently Viewed</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Recently Viewed Section -->
    <section class="recently-viewed section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">Recently Viewed Products</h2>

                    @if(count($products) > 0)
                        <div class="row">
                            @foreach($products as $product)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="single-product">
                                        <div class="product-img">
                                            <a href="{{ route('front.product-detail', $product->slug) }}">
                                                @if($product->getFirstMediaUrl('product_images'))
                                                    <img class="default-img" src="{{ $product->getFirstMediaUrl('product_images') }}" alt="{{ $product->title }}">
                                                @else
                                                    <img class="default-img" src="{{ asset('frontend/img/no-image.png') }}" alt="{{ $product->title }}">
                                                @endif
                                            </a>
                                            <div class="button-head">
                                                <div class="product-action">
                                                    <a data-toggle="modal" data-target="#exampleModal" title="Visualização rápida" href="#"><i class=" ti-eye"></i><span>Ver rápido</span></a>
                                                    <a title="Favoritos" href="#"><i class=" ti-heart "></i><span>Adicionar aos favoritos</span></a>
                                                    <a title="Comparar" href="#"><i class="ti-bar-chart-alt"></i><span>Comparar</span></a>
                                                </div>
                                                <div class="product-action-2">
                                                    <a title="Adicionar ao carrinho" href="{{ route('add-to-cart', $product->id) }}">Adicionar ao carrinho</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                            <div class="product-price">
                                                @if($product->discount > 0)
                                                    <span class="old">{{ format_currency((float) ($product->price)) }}</span>
                                                    <span>{{ format_currency((float) ($product->price - ($product->price * $product->discount / 100))) }}</span>
                                                @else
                                                    <span>{{ format_currency((float) ($product->price)) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti-eye fa-3x text-muted mb-3"></i>
                            <h5>No recently viewed products</h5>
                            <p class="text-muted">You haven't viewed any products yet. Start browsing our store!</p>
                            <a href="{{ route('front.product-grids') }}" class="btn btn-primary">
                                Ver produtos
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- End Recently Viewed Section -->

@endsection
