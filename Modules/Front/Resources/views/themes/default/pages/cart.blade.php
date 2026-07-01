@php
    use Illuminate\Support\Str;
    use Modules\Core\Helpers\Helper;

    $cartItems = Helper::getAllProductFromCart();
    $subtotal = Helper::totalCartPrice();
    $couponValue = session('coupon.value') ?? session('coupon.discount') ?? 0;
    $totalAmount = max(0, $subtotal - $couponValue);
@endphp

@extends('front::layouts.master')

@section('title', 'Carrinho')

@section('content')
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Inicio<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Carrinho</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="shopping-cart section">
        <div class="container">
            @if($cartItems->isEmpty())
                <div class="alert alert-warning">
                    Seu carrinho esta vazio.
                    <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                </div>
            @else
                <form action="{{ route('cart-update') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table shopping-summery">
                            <thead>
                            <tr class="main-hading">
                                <th>Produto</th>
                                <th>Descricao</th>
                                <th class="text-center">Preco</th>
                                <th class="text-center">Quantidade</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Remover</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cartItems as $key => $cart)
                                @php
                                    $photo = $cart->product?->image_url;
                                    if (! $photo && $cart->product?->photo) {
                                        $photo = explode(',', $cart->product->photo)[0] ?? null;
                                    }
                                @endphp
                                <tr>
                                    <td class="image">
                                        <img src="{{ $photo ?: asset('backend/img/thumbnail-default.jpg') }}" alt="{{ $cart->product?->title ?? 'Produto' }}">
                                    </td>
                                    <td class="product-des">
                                        <p class="product-name">
                                            @if($cart->product?->slug)
                                                <a href="{{ route('front.product-detail', $cart->product->slug) }}" target="_blank">
                                                    {{ $cart->product->title }}
                                                </a>
                                            @else
                                                <span>Produto indisponivel</span>
                                            @endif
                                        </p>
                                        <p class="product-des">{{ Str::limit(strip_tags((string) ($cart->summary ?? $cart->product?->summary)), 120) }}</p>
                                    </td>
                                    <td class="price text-center">
                                        <span>R$ {{ number_format((float) $cart->price, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="qty text-center">
                                        <input type="number" name="quantity[{{ $key }}]" class="input-number" data-min="1" data-max="100" min="1" max="100" value="{{ $cart->quantity }}">
                                        <input type="hidden" name="qty_id[]" value="{{ $cart->id }}">
                                    </td>
                                    <td class="total-amount text-center">
                                        <span>R$ {{ number_format((float) $cart->amount, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="action text-center">
                                        <a href="{{ route('cart-delete', $cart->id) }}" title="Remover"><i class="ti-trash remove-icon"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-right mb-4">
                        <button class="btn" type="submit">Atualizar carrinho</button>
                    </div>
                </form>

                <div class="total-amount">
                    <div class="row">
                        <div class="col-lg-8 col-md-5 col-12">
                            <div class="left">
                                @include('front::partials.shipping-quote', ['context' => 'cart'])

                                <div class="coupon">
                                    <form action="{{ route('coupon-store') }}" method="POST">
                                        @csrf
                                        <input name="code" placeholder="Digite seu cupom">
                                        <button class="btn" type="submit">Aplicar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-7 col-12">
                            <div class="right">
                                <ul>
                                    <li>Subtotal<span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span></li>
                                    @if($couponValue > 0)
                                        <li>Desconto<span>-R$ {{ number_format($couponValue, 2, ',', '.') }}</span></li>
                                    @endif
                                    <li class="last">Total<span>R$ {{ number_format($totalAmount, 2, ',', '.') }}</span></li>
                                </ul>
                                <div class="button5">
                                    <a href="{{ route('front.checkout') }}" class="btn">Finalizar compra</a>
                                    <a href="{{ route('front.product-grids') }}" class="btn">Continuar comprando</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
