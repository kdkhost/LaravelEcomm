@php
    use Modules\Core\Helpers\Helper;

    $user = Auth::user();
    $defaultAddress = $user?->defaultShippingAddress();
    $cartItems = Helper::getAllProductFromCart();
    $subtotal = Helper::totalCartPrice();
    $couponValue = session('coupon.value') ?? session('coupon.discount') ?? 0;
    $totalAmount = max(0, $subtotal - $couponValue);
    $themePath = 'front::themes.modern';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Finalizar compra')

@section('content')
    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li class="active">Finalizar compra</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            @if($cartItems->isEmpty())
                <div class="alert alert-warning">
                    Seu carrinho esta vazio.
                    <a href="{{ route('front.product-grids') }}">Continuar comprando</a>
                </div>
            @else
                <form method="POST" action="{{ route('front.cart.order') }}">
                    @csrf
                    <input type="hidden" name="cart" value="1">
                    <input type="hidden" name="country" value="BR">

                    <div class="row">
                        <div class="main col-md-8">
                            <h1 class="page-title">Dados de entrega</h1>
                            <div class="separator-2"></div>
                            <p>Confira seus dados para concluir a compra com seguranca.</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="first_name" value="{{ old('first_name', $defaultAddress?->first_name) }}" required>
                                        @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sobrenome <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="last_name" value="{{ old('last_name', $defaultAddress?->last_name) }}" required>
                                        @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>E-mail <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="email" value="{{ old('email', $defaultAddress?->email ?? $user?->email) }}" required>
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telefone <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="phone" data-mask-phone value="{{ old('phone', $defaultAddress?->phone) }}" required>
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input class="form-control" type="text" name="post_code" data-mask-cep value="{{ old('post_code', $defaultAddress?->post_code) }}">
                                        @error('post_code') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Endereco <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="address1" value="{{ old('address1', $defaultAddress?->address1) }}" required>
                                        @error('address1') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input class="form-control" type="text" name="address2" value="{{ old('address2', $defaultAddress?->address2) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input class="form-control" type="text" name="city" value="{{ old('city', $defaultAddress?->city) }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>UF</label>
                                        <input class="form-control" type="text" name="state" maxlength="2" value="{{ old('state', $defaultAddress?->state) }}">
                                    </div>
                                </div>

                                @if($user)
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="save_address" value="1"> Salvar este endereco</label>
                                        </div>
                                        <div class="checkbox">
                                            <label><input type="checkbox" name="make_default_address" value="1"> Tornar endereco padrao</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <aside class="col-md-4">
                            <div class="sidebar">
                                <div class="block clearfix">
                                    <h3 class="title">Resumo</h3>
                                    <div class="separator-2"></div>
                                    <table class="table">
                                        <tr class="order_subtotal" data-price="{{ $subtotal }}">
                                            <td>Subtotal</td>
                                            <td class="text-right">R$ {{ number_format($subtotal, 2, ',', '.') }}</td>
                                        </tr>
                                        @if(count(Helper::shipping()) > 0 && Helper::cartCount() > 0)
                                            <tr>
                                                <td colspan="2">
                                                    <label>Frete</label>
                                                    <select name="shipping" class="form-control">
                                                        <option value="" data-price="0">Selecione o frete</option>
                                                        @foreach(Helper::shipping() as $shipping)
                                                            <option value="{{ $shipping->id }}" data-price="{{ $shipping->price }}">
                                                                {{ $shipping->type }}: R$ {{ number_format($shipping->price, 2, ',', '.') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>Frete</td>
                                                <td class="text-right">Gratis</td>
                                            </tr>
                                        @endif
                                        @if($couponValue > 0)
                                            <tr class="coupon_price" data-price="{{ $couponValue }}">
                                                <td>Desconto</td>
                                                <td class="text-right">-R$ {{ number_format($couponValue, 2, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr id="order_total_price">
                                            <th>Total</th>
                                            <th class="text-right">R$ {{ number_format($totalAmount, 2, ',', '.') }}</th>
                                        </tr>
                                    </table>
                                </div>

                                <div class="block clearfix">
                                    <h3 class="title">Pagamento</h3>
                                    <div class="separator-2"></div>
                                    <div class="radio">
                                        <label><input name="payment_method" type="radio" value="mercadopago" checked> Mercado Pago (Pix, cartao e boleto)</label>
                                    </div>
                                    <div class="radio">
                                        <label><input name="payment_method" type="radio" value="cod"> Pagamento na entrega</label>
                                    </div>
                                    <div class="radio">
                                        <label><input name="payment_method" type="radio" value="paypal"> PayPal</label>
                                    </div>
                                    <div class="radio">
                                        <label><input name="payment_method" type="radio" value="stripe"> Stripe</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-default btn-block">Finalizar compra</button>
                            </div>
                        </aside>
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
            document.querySelector('#order_total_price th:last-child').textContent = total.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        });
    </script>
@endpush
