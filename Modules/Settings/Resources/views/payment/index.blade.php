@extends('admin::layouts.master')

@section('title', 'Configurações de pagamento')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Configurações de pagamento</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('settings.payment.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h4>Mercado Pago</h4>
                            <div class="alert alert-info">
                                <strong>Webhook:</strong> <code>{{ $mercadoPagoWebhookUrl }}</code><br>
                                <strong>Retorno:</strong> <code>{{ $mercadoPagoReturnUrl }}</code>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="mercadopago_enabled"
                                           name="mercadopago_enabled" value="1"
                                           {{ ($paymentSettings['mercadopago_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mercadopago_enabled">Habilitar Mercado Pago</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mercadopago_environment">Ambiente</label>
                                <select class="form-control" id="mercadopago_environment" name="mercadopago_environment">
                                    <option value="sandbox" {{ ($paymentSettings['mercadopago_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="production" {{ ($paymentSettings['mercadopago_environment'] ?? '') === 'production' ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mercadopago_access_token">Access Token</label>
                                <input type="password" class="form-control" id="mercadopago_access_token"
                                       name="mercadopago_access_token"
                                       value="{{ $paymentSettings['mercadopago_access_token'] ?? '' }}"
                                       autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="mercadopago_public_key">Public Key</label>
                                <input type="text" class="form-control" id="mercadopago_public_key"
                                       name="mercadopago_public_key"
                                       value="{{ $paymentSettings['mercadopago_public_key'] ?? '' }}"
                                       autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="mercadopago_webhook_secret">Segredo do webhook</label>
                                <input type="text" class="form-control" id="mercadopago_webhook_secret"
                                       name="mercadopago_webhook_secret"
                                       value="{{ $paymentSettings['mercadopago_webhook_secret'] ?? '' }}"
                                       placeholder="Use um texto forte para proteger a URL do webhook">
                            </div>
                            <div class="form-group">
                                <label for="mercadopago_statement_descriptor">Nome na fatura</label>
                                <input type="text" class="form-control" id="mercadopago_statement_descriptor"
                                       name="mercadopago_statement_descriptor" maxlength="22"
                                       value="{{ $paymentSettings['mercadopago_statement_descriptor'] ?? 'LOJA VIRTUAL' }}">
                            </div>

                            <hr>

                            <h4>Stripe</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="stripe_enabled"
                                           name="stripe_enabled" value="1"
                                           {{ ($paymentSettings['stripe_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stripe_enabled">Habilitar Stripe</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="stripe_public_key">Public Key do Stripe</label>
                                <input type="text" class="form-control" id="stripe_public_key"
                                       name="stripe_public_key"
                                       value="{{ $paymentSettings['stripe_public_key'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="stripe_secret_key">Secret Key do Stripe</label>
                                <input type="password" class="form-control" id="stripe_secret_key"
                                       name="stripe_secret_key"
                                       value="{{ $paymentSettings['stripe_secret_key'] ?? '' }}">
                            </div>

                            <h4 class="mt-4">PayPal</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="paypal_enabled"
                                           name="paypal_enabled" value="1"
                                           {{ ($paymentSettings['paypal_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paypal_enabled">Habilitar PayPal</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="paypal_client_id">Client ID do PayPal</label>
                                <input type="text" class="form-control" id="paypal_client_id"
                                       name="paypal_client_id"
                                       value="{{ $paymentSettings['paypal_client_id'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="paypal_client_secret">Client Secret do PayPal</label>
                                <input type="password" class="form-control" id="paypal_client_secret"
                                       name="paypal_client_secret"
                                       value="{{ $paymentSettings['paypal_client_secret'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="paypal_mode">Ambiente do PayPal</label>
                                <select class="form-control" id="paypal_mode" name="paypal_mode">
                                    <option value="sandbox" {{ ($paymentSettings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ ($paymentSettings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>

                            <h4 class="mt-4">Outras formas de pagamento</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="cod_enabled"
                                           name="cod_enabled" value="1"
                                           {{ ($paymentSettings['cod_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cod_enabled">Habilitar pagamento na entrega</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="bank_transfer_enabled"
                                           name="bank_transfer_enabled" value="1"
                                           {{ ($paymentSettings['bank_transfer_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bank_transfer_enabled">Habilitar transferência bancária</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bank_account_details">Dados bancários</label>
                                <textarea class="form-control" id="bank_account_details"
                                          name="bank_account_details" rows="3">{{ $paymentSettings['bank_account_details'] ?? '' }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Salvar configurações</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
