@extends($themePath . '.layouts.master')

@section('title', 'Início - ' . ($settings['site-name'] ?? 'E-commerce Website'))
@section('description', $settings['short_des'] ?? 'Loja virtual moderna com recursos avançados')

@section('content')

<!-- banner start -->
<!-- ================ -->
<div class="banner clearfix">

    <!-- slideshow start -->
    <!-- ================ -->
    <div class="slideshow">

        <!-- slider revolution start -->
        <!-- ================ -->
        <div id="main-slider" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $key => $banner)
                        <li data-target="#main-slider" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                    @endforeach
                @else
                    <li data-target="#main-slider" data-slide-to="0" class="active"></li>
                @endif
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                        <div class="item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ $banner->imageUrl }}" alt="{{ $banner->title }}" style="width:100%; height: 500px; object-fit: cover;">
                            <div class="carousel-caption">
                                <h2 class="title text-white" style="color: #fff; font-size: 48px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">{{ $banner->title }}</h2>
                                <div class="separator-2 light"></div>
                                <p style="color: #fff; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ Str::limit($banner->description, 150) }}</p>
                                <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                                    Comprar agora <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item active">
                        <img src="{{ route('front.placeholder.image', ['type' => 'banner', 'text' => $settings['site-name'] ?? 'E-commerce']) }}" alt="Welcome" style="width:100%; height: 500px; object-fit: cover;">
                        <div class="carousel-caption">
                            <h2 class="title text-white" style="color: #fff; font-size: 48px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Bem-vindo à {{ $settings['site-name'] ?? 'E-commerce' }}</h2>
                            <div class="separator-2 light"></div>
                            <p style="color: #fff; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ $settings['description'] ?? 'Descubra produtos com ótimos preços e qualidade.' }}</p>
                            <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                                Comprar agora <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#main-slider" role="button" data-slide="prev">
                <span class="icon-prev" aria-hidden="true"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="right carousel-control" href="#main-slider" role="button" data-slide="next">
                <span class="icon-next" aria-hidden="true"></span>
                <span class="sr-only">Próximo</span>
            </a>
        </div>
        <!-- slider revolution end -->

    </div>
    <!-- slideshow end -->

</div>
<!-- banner end -->

<div id="page-start"></div>

<!-- section start -->
<!-- ================ -->
<section class="section light-gray-bg clearfix">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- pills start -->
                <!-- ================ -->
                <!-- Nav tabs -->
                <ul class="nav nav-pills" role="tablist">
                    <li class="active"><a href="#pill-1" role="tab" data-toggle="tab" title="Novidades"><i class="icon-star"></i> Novidades</a></li>
                    <li><a href="#pill-2" role="tab" data-toggle="tab" title="Destaques"><i class="icon-heart"></i> Destaques</a></li>
                    <li><a href="#pill-3" role="tab" data-toggle="tab" title="Mais vendidos"><i class="icon-up-1"></i> Mais vendidos</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content clear-style">
                    <div class="tab-pane active" id="pill-1">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            @if(isset($latest_products) && $latest_products->count() > 0)
                                @foreach($latest_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Adicionar aos favoritos</a>
                                                        <a href="{{ route('front.virtual-try-on', ['slug' => $product->slug]) }}" class="btn-sm-link"><i class="fa fa-magic pr-10"></i>Provador virtual</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>Ver detalhes</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ format_currency((float) ($product->price)) }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
                                                        Adicionar ao carrinho<i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h3>Nenhum produto disponível</h3>
                                        <p>Volte em breve para ver novidades.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-2">
                        <div class="row masonry-grid-fitrows grid-space- 10">
                            @if(isset($featured_products) && $featured_products->count() > 0)
                                @foreach($featured_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Adicionar aos favoritos</a>
                                                        <a href="{{ route('front.virtual-try-on', ['slug' => $product->slug]) }}" class="btn-sm-link"><i class="fa fa-magic pr-10"></i>Provador virtual</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>Ver detalhes</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ format_currency((float) ($product->price)) }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
                                                        Adicionar ao carrinho<i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h3>Produtos em destaque</h3>
                                        <p>Nenhum produto em destaque disponível</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-3">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            @if(isset($hot_products) && $hot_products->count() > 0)
                                @foreach($hot_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Adicionar aos favoritos</a>
                                                        <a href="{{ route('front.virtual-try-on', ['slug' => $product->slug]) }}" class="btn-sm-link"><i class="fa fa-magic pr-10"></i>Provador virtual</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>Ver detalhes</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ format_currency((float) ($product->price)) }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
                                                        Adicionar ao carrinho<i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h3>Mais vendidos</h3>
                                        <p>Nenhum mais vendido disponível</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- pills end -->
            </div>
        </div>
    </div>
</section>
<!-- section end -->

<!-- section start -->
<!-- ================ -->
<section class="section clearfix">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center title">Por que comprar aqui?</h1>
                <div class="separator"></div>
                <p class="text-center">{{ $settings['short_des'] ?? 'Oferecemos uma experiência de compra segura, produtos de qualidade e atendimento eficiente.' }}</p>
                <div class="row grid-space-20">
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <i class="fa fa-truck text-default"></i>
                            <h2>Frete grátis</h2>
                            <p>Frete grátis em pedidos acima de R$ 50,00, com entrega rápida e confiável.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
                            <i class="fa fa-shield text-default"></i>
                            <h2>Pagamento seguro</h2>
                            <p>Your payment information is safe and secure with our encrypted payment system.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="400">
                            <i class="fa fa-refresh text-default"></i>
                            <h2>Troca fácil</h2>
                            <p>Política de devolução em até 30 dias, com trocas simples.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="600">
                            <i class="fa fa-headphones text-default"></i>
                            <h2>Atendimento</h2>
                            <p>Atendimento preparado para ajudar em dúvidas, pedidos e pós-venda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section end -->

@endsection

@push('scripts')
@endpush
