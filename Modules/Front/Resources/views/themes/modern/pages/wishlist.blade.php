@php use Modules\Core\Helpers\Helper; @endphp
@extends($themePath . '.layouts.master')
@section('title','Favoritos')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>Favoritos</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Início</a></li>
            <li class="active">Favoritos</li>
        </ol>
    </div></div></div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-shopping-cart">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Nome</th>
                            <th class="text-center">Preço</th>
                            <th class="text-center">Ação</th>
                            <th class="text-center">Remover</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(Helper::getAllProductFromWishlist())
                        @foreach(Helper::getAllProductFromWishlist() as $wishlist)
                        <tr>
                            @php $photo = explode(',', $wishlist->product['photo']); @endphp
                            <td><img src="{{ $photo[0] }}" alt="{{ $wishlist->product['title'] }}" style="width:80px;"></td>
                            <td>
                                <a href="{{ route('front.product-detail', $wishlist->product['slug']) }}">
                                    {{ $wishlist->product['title'] }}
                                </a>
                                <p class="small">{!! $wishlist['summary'] !!}</p>
                            </td>
                            <td class="text-center">{{ format_currency((float) ($wishlist['amount'])) }}</td>
                            <td class="text-center">
                                <a href="{{ route('add-to-cart', $wishlist->product['slug']) }}" class="btn btn-sm btn-default">Adicionar ao carrinho</a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('wishlist-delete', $wishlist->id) }}" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">
                                Sua lista de favoritos está vazia. <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@include($themePath . '.layouts.newsletter')
@endsection
