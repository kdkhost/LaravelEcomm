@php
    use Illuminate\Support\Str;
    use Modules\Core\Helpers\Helper;

    $cartItems = Helper::getAllProductFromCart();
    $subtotal = Helper::totalCartPrice();
    $couponValue = session('coupon.value') ?? session('coupon.discount') ?? 0;
    $totalAmount = max(0, $subtotal - $couponValue);
    $themePath = 'front::themes.modern';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Carrinho')

@section('content')
    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li class="active">Carrinho</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            <div class="row">
                <div class="main col-md-12">
                    <h1 class="page-title">Carrinho</h1>
                    <div class="separator-2"></div>

                    @if($cartItems->isEmpty())
                        <div class="alert alert-warning">
                            Seu carrinho esta vazio.
                            <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                        </div>
                    @else
                        <form action="{{ route('cart-update') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table cart">
                                    <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preco</th>
                                        <th>Quantidade</th>
                                        <th class="amount">Total</th>
                                        <th>Acao</th>
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
                                            <td class="product">
                                                <div class="media">
                                                    <div class="media-left">
                                                        <img src="{{ $photo ?: asset('backend/img/thumbnail-default.jpg') }}" alt="{{ $cart->product?->title ?? 'Produto' }}" style="width:60px;height:60px;object-fit:cover;">
                                                    </div>
                                                    <div class="media-body">
                                                        <h5 class="media-heading">
                                                            @if($cart->product?->slug)
                                                                <a href="{{ route('front.product-detail', $cart->product->slug) }}">{{ $cart->product->title }}</a>
                                                            @else
                                                                <span>Produto indisponivel</span>
                                                            @endif
                                                        </h5>
                                                        <small>{{ Str::limit(strip_tags((string) ($cart->summary ?? $cart->product?->summary)), 70) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="price">R$ {{ number_format((float) $cart->price, 2, ',', '.') }}</td>
                                            <td class="quantity">
                                                <input type="number" name="quantity[{{ $key }}]" class="form-control" value="{{ $cart->quantity }}" min="1" max="100" style="width:70px;">
                                                <input type="hidden" name="qty_id[]" value="{{ $cart->id }}">
                                            </td>
                                            <td class="amount">R$ {{ number_format((float) $cart->amount, 2, ',', '.') }}</td>
                                            <td>
                                                <a href="{{ route('cart-delete', $cart->id) }}" class="btn btn-danger btn-sm" title="Remover">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <a href="{{ route('front.product-grids') }}" class="btn btn-default">
                                        <i class="icon-left-open-big"></i> Continuar comprando
                                    </a>
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Atualizar carrinho
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <table class="table cart-total pull-right" style="width:auto;">
                                        <tbody>
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td class="amount">R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                                        </tr>
                                        @if($couponValue > 0)
                                            <tr>
                                                <td>Desconto:</td>
                                                <td class="amount">-R$ {{ number_format($couponValue, 2, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td class="total-amount"><strong>R$ {{ number_format($totalAmount, 2, ',', '.') }}</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-md-6">
                                @include('front::partials.shipping-quote', ['context' => 'cart'])

                                <div class="coupon">
                                    <h4>Aplicar cupom</h4>
                                    <form action="{{ route('coupon-store') }}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control" placeholder="Digite seu cupom">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit">Aplicar</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('front.checkout') }}" class="btn btn-default">
                                    Finalizar compra <i class="icon-right-open-big"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
