@extends($themePath . '.layouts.master')
@section('title','E-SHOP || Kits')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>Kits de produtos</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Início</a></li>
            <li class="active">Kits</li>
        </ol>
    </div></div></div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row isotope-container">
            @foreach($bundles as $bundle)
            <div class="col-md-4 col-sm-6 isotope-item">
                <div class="product-item">
                    <div class="product-item-img">
                        <a href="{{ route('front.bundle_detail', $bundle->slug) }}">
                            <img src="{{ $bundle->imageUrl }}" alt="{{ $bundle->title }}" class="img-responsive">
                        </a>
                        @if($bundle->discount)
                        <span class="badge badge-danger">Economize {{ $bundle->discount }}%</span>
                        @endif
                    </div>
                    <div class="product-item-title">
                        <a href="{{ route('front.bundle_detail', $bundle->slug) }}">{{ $bundle->title }}</a>
                    </div>
                    <div class="product-item-price">
                        @php $after_discount = ($bundle->price - ($bundle->price * $bundle->discount) / 100); @endphp
                        @if($bundle->discount)<del class="text-muted">{{ format_currency((float) ($bundle->price)) }}</del>@endif
                        <span class="text-default">{{ format_currency((float) ($after_discount)) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center">
            {{ $bundles->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
</section>
@endsection
