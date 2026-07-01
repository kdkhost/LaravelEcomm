@php
    use Modules\Core\Helpers\Helper;

    $user = Auth::user();
    $defaultAddress = $user?->defaultShippingAddress();
    $cartItems = Helper::getAllProductFromCart();
    $subtotal = Helper::totalCartPrice();
    $couponValue = session('coupon.value') ?? session('coupon.discount') ?? 0;
    $totalAmount = max(0, $subtotal - $couponValue);
    $themePath = 'front::themes.default';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Finalizar compra')

@section('content')
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Início<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Finalizar compra</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="shop checkout section">
        <div class="container">
            @if($cartItems->isEmpty())
                <div class="alert alert-warning">
                    Seu carrinho está vazio.
                    <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                </div>
            @else
                <form class="form" method="POST" action="{{ route('front.cart.order') }}">
                    @csrf
                    <input type="hidden" name="cart" value="1">
                    <input type="hidden" name="country" value="BR">

                    <div class="row">
                        <div class="col-lg-8 col-12">
                            <div class="checkout-form">
                                <h2>Dados de entrega</h2>
                                <p>Confira seus dados para concluir a compra com segurança.</p>

                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Nome<span>*</span></label>
                                            <input type="text" name="first_name" value="{{ old('first_name', $defaultAddress?->first_name) }}" required>
                                            @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Sobrenome<span>*</span></label>
                                            <input type="text" name="last_name" value="{{ old('last_name', $defaultAddress?->last_name) }}" required>
                                            @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>E-mail<span>*</span></label>
                                            <input type="email" name="email" value="{{ old('email', $defaultAddress?->email ?? $user?->email) }}" required>
                                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Telefone<span>*</span></label>
                                            <input type="text" name="phone" data-mask-phone value="{{ old('phone', $defaultAddress?->phone) }}" required>
                                            @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-12">
                                        <div class="form-group">
                                            <label>CEP</label>
                                            <input type="text" name="post_code" data-mask-cep value="{{ old('post_code', $defaultAddress?->post_code) }}">
                                            @error('post_code') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-12">
                                        <div class="form-group">
                                            <label>Endereço<span>*</span></label>
                                            <input type="text" name="address1" value="{{ old('address1', $defaultAddress?->address1) }}" required>
                                            @error('address1') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Complemento</label>
                                            <input type="text" name="address2" value="{{ old('address2', $defaultAddress?->address2) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-8">
                                        <div class="form-group">
                                            <label>Cidade</label>
                                            <input type="text" name="city" value="{{ old('city', $defaultAddress?->city) }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-4">
                                        <div class="form-group">
                                            <label>UF</label>
                                            <input type="text" name="state" maxlength="2" value="{{ old('state', $defaultAddress?->state) }}">
                                        </div>
                                    </div>

                                    @if($user)
                                        <div class="col-lg-12 col-12">
                                            <label class="checkbox-inline" style="display: flex; align-items: center; gap: 10px;">
                                                <input type="checkbox" name="save_address" value="1" style="width: auto;">
                                                Salvar este endereço
                                            </label>
                                            <label class="checkbox-inline" style="display: flex; align-items: center; gap: 10px;">
                                                <input type="checkbox" name="make_default_address" value="1" style="width: auto;">
                                                Tornar endereço padrão
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="order-details">
                                <div class="single-widget">
                                    <h2>Resumo</h2>
                                    <div class="content">
                                        <ul>
                                            <li class="order_subtotal" data-price="{{ $subtotal }}">
                                                Subtotal<span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                                            </li>
                                            @if(count(Helper::shipping()) > 0 && Helper::cartCount() > 0)
                                                <li class="shipping">
                                                    Frete
                                                    <select name="shipping" class="nice-select">
                                                        <option value="" data-price="0">Selecione o frete</option>
                                                        @foreach(Helper::shipping() as $shipping)
                                                            <option value="{{ $shipping->id }}" data-price="{{ $shipping->price }}">
                                                                {{ $shipping->type }}: R$ {{ number_format($shipping->price, 2, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </li>
                                            @else
                                                <li>Frete<span>Grátis</span></li>
                                            @endif
                                            @if($couponValue > 0)
                                                <li class="coupon_price" data-price="{{ $couponValue }}">
                                                    Desconto<span>-R$ {{ number_format($couponValue, 2, ',', '.') }}</span>
                                                </li>
                                            @endif
                                            <li class="last" id="order_total_price">
                                                Total<span>R$ {{ number_format($totalAmount, 2, ',', '.') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="single-widget">
                                    <h2>Pagamento</h2>
                                    <div class="content">
                                        <label><input name="payment_method" type="radio" value="mercadopago" checked> Mercado Pago (Pix, cartão e boleto)</label><br>
                                        <label><input name="payment_method" type="radio" value="cod"> Pagamento na entrega</label><br>
                                        <label><input name="payment_method" type="radio" value="paypal"> PayPal</label><br>
                                        <label><input name="payment_method" type="radio" value="stripe"> Stripe</label>
                                    </div>
                                </div>

                                <div class="single-widget get-button">
                                    <div class="content">
                                        <div class="button">
                                            <button type="submit" class="btn">Finalizar compra</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function onlyDigits(value) {
            return (value || '').replace(/\D/g, '');
        }

        function formatPhone(value) {
            const digits = onlyDigits(value).slice(0, 11);
            if (digits.length <= 10) {
                return digits.replace(/^(\d{0,2})(\d{0,4})(\d{0,4}).*/, function (_, ddd, first, last) {
                    return [ddd && '(' + ddd, ddd && ') ', first, last && '-' + last].filter(Boolean).join('');
                });
            }

            return digits.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        }

        function formatCep(value) {
            return onlyDigits(value).slice(0, 8).replace(/^(\d{5})(\d{0,3}).*/, '$1-$2');
        }

        document.querySelectorAll('[data-mask-phone]').forEach(function (input) {
            input.addEventListener('input', function () {
                input.value = formatPhone(input.value);
            });
        });

        document.querySelectorAll('[data-mask-cep]').forEach(function (input) {
            input.addEventListener('input', function () {
                input.value = formatCep(input.value);
            });

            input.addEventListener('blur', async function () {
                const cep = onlyDigits(input.value);
                if (cep.length !== 8) {
                    return;
                }

                const response = await fetch('https://viacep.com.br/ws/' + cep + '/json/');
                const data = await response.json();
                if (data.erro) {
                    return;
                }

                document.querySelector('[name=address1]').value = data.logradouro || '';
                document.querySelector('[name=city]').value = data.localidade || '';
                document.querySelector('[name=state]').value = data.uf || '';
            });
        });

        document.querySelector('select[name=shipping]')?.addEventListener('change', function () {
            const subtotal = parseFloat(document.querySelector('.order_subtotal')?.dataset.price || 0);
            const coupon = parseFloat(document.querySelector('.coupon_price')?.dataset.price || 0);
            const shipping = parseFloat(this.options[this.selectedIndex]?.dataset.price || 0);
            const total = Math.max(0, subtotal + shipping - coupon);
            document.querySelector('#order_total_price span').textContent = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        });
    </script>
@endpush
