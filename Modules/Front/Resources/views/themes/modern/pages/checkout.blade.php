@php 
use Modules\Core\Helpers\Helper;
$user = Auth::user();
$defaultAddress = $user?->defaultShippingAddress();
$cartItems = Helper::getAllProductFromCart();
$subtotal = Helper::totalCartPrice();
@endphp
@extends($themePath . '.layouts.master')
@section('title','Finalizar Compra')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Início</a></li>
            <li class="active">Finalizar Compra</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">Finalizar Compra</h1>
                <div class="separator-2"></div>
                
                @if($cartItems->isEmpty())
                    <div class="alert alert-warning">
                        Seu carrinho está vazio. <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                    </div>
                @else
                <form method="POST" action="{{ route('front.cart.order') }}">
                    @csrf
                    
                    <!-- Cart Summary -->
                    <table class="table cart">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Qtd</th>
                                <th class="amount">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td class="product">
                                    <a href="{{ route('front.product-detail', $item->product->slug) }}">{{ $item->product->title }}</a>
                                    <small>{{ Str::limit($item->product->summary, 50) }}</small>
                                </td>
                                <td class="price">R$ {{ number_format($item->price, 2) }}</td>
                                <td class="quantity">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="{{ $item->quantity }}" readonly>
                                    </div>
                                </td>
                                <td class="amount">R$ {{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="total-quantity" colspan="3">Subtotal</td>
                                <td class="amount">R$ {{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if(session('coupon'))
                            <tr>
                                <td class="total-quantity" colspan="3">Desconto</td>
                                <td class="amount">-R$ {{ number_format(session('coupon.value'), 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="total-quantity" colspan="3">Total</td>
                                <td class="total-amount">R$ {{ number_format($subtotal - (session('coupon.value') ?? 0), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="space-bottom"></div>
                    
                    <!-- Billing Information -->
                    <fieldset>
                        <legend>Informações de Cobrança</legend>
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-lg-3">
                                    <h3 class="title">Dados Pessoais</h3>
                                </div>
                                <div class="col-lg-8 col-lg-offset-1">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Nome<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $defaultAddress?->first_name) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Sobrenome<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $defaultAddress?->last_name) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Telefone<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $defaultAddress?->phone) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">E-mail<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $defaultAddress?->email ?? $user?->email) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space"></div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <h3 class="title">Seu Endereço</h3>
                                </div>
                                <div class="col-lg-8 col-lg-offset-1">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">CEP<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="post_code" class="form-control" value="{{ old('post_code', $defaultAddress?->post_code) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Endereço<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="address1" class="form-control" value="{{ old('address1', $defaultAddress?->address1) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Número<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="number" class="form-control" placeholder="Nº" value="{{ old('number') }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Complemento</label>
                                        <div class="col-md-10">
                                            <input type="text" name="address2" class="form-control" value="{{ old('address2', $defaultAddress?->address2) }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Bairro<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="neighborhood" class="form-control" placeholder="Bairro" value="{{ old('neighborhood') }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Cidade<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="city" class="form-control" value="{{ old('city', $defaultAddress?->city) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Estado (UF)<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <select name="state" class="form-control">
                                                <option value="">Selecione...</option>
                                                <option value="AC">Acre</option>
                                                <option value="AL">Alagoas</option>
                                                <option value="AP">Amapá</option>
                                                <option value="AM">Amazonas</option>
                                                <option value="BA">Bahia</option>
                                                <option value="CE">Ceará</option>
                                                <option value="DF">Distrito Federal</option>
                                                <option value="ES">Espírito Santo</option>
                                                <option value="GO">Goiás</option>
                                                <option value="MA">Maranhão</option>
                                                <option value="MT">Mato Grosso</option>
                                                <option value="MS">Mato Grosso do Sul</option>
                                                <option value="MG">Minas Gerais</option>
                                                <option value="PA">Pará</option>
                                                <option value="PB">Paraíba</option>
                                                <option value="PR">Paraná</option>
                                                <option value="PE">Pernambuco</option>
                                                <option value="PI">Piauí</option>
                                                <option value="RJ">Rio de Janeiro</option>
                                                <option value="RN">Rio Grande do Norte</option>
                                                <option value="RS">Rio Grande do Sul</option>
                                                <option value="RO">Rondônia</option>
                                                <option value="RR">Roraima</option>
                                                <option value="SC">Santa Catarina</option>
                                                <option value="SP">São Paulo</option>
                                                <option value="SE">Sergipe</option>
                                                <option value="TO">Tocantins</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">País<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <select name="country" class="form-control">
                                                <option value="BR" {{ old('country', $defaultAddress?->country) == 'BR' ? 'selected' : '' }}>Brasil</option>
                                                <option value="US" {{ old('country', $defaultAddress?->country) == 'US' ? 'selected' : '' }}>Estados Unidos</option>
                                                <option value="AR" {{ old('country', $defaultAddress?->country) == 'AR' ? 'selected' : '' }}>Argentina</option>
                                                <option value="PY" {{ old('country', $defaultAddress?->country) == 'PY' ? 'selected' : '' }}>Paraguai</option>
                                                <option value="UY" {{ old('country', $defaultAddress?->country) == 'UY' ? 'selected' : '' }}>Uruguai</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">CPF / CNPJ <small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="document" class="form-control" placeholder="000.000.000-00" value="{{ old('document') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    
                    <!-- Payment Method -->
                    <fieldset>
                        <legend>Forma de Pagamento</legend>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <i class="fa fa-money pr-10"></i> Pagar na Entrega
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="stripe">
                                        <i class="fa fa-credit-card pr-10"></i> Cartão de Crédito (Stripe)
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="paypal">
                                        <i class="fa fa-paypal pr-10"></i> PayPal
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="mercadopago">
                                        <i class="fa fa-credit-card pr-10"></i> MercadoPago
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    
                    <div class="text-right">
                        <a href="{{ route('cart-list') }}" class="btn btn-group btn-default">
                            <i class="icon-left-open-big"></i> Voltar ao Carrinho
                        </a>
                        <button type="submit" class="btn btn-group btn-default">
                            Finalizar Pedido <i class="icon-right-open-big"></i>
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{asset('frontend/js/jquery.mask.min.js')}}"></script>
<script>
$(document).ready(function() {
    var maskBehavior = function(val) {
        return val.replace(/\D/g, '').length <= 11 ? '000.000.000-009' : '00.000.000/0000-00';
    },
    spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(maskBehavior.apply({}, arguments), options);
        }
    };
    $('input[name="document"]').mask(maskBehavior, spOptions);
    $('input[name="phone"]').mask('(00) 00000-0000');
    $('input[name="post_code"]').mask('00000-000');
});
</script>
@endpush