@extends('admin::layouts.master')

@section('title', 'Configuracoes de frete')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Configuracoes de frete</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('settings.shipping.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="default_shipping_method">Metodo padrao de frete</label>
                                        <select class="form-control" id="default_shipping_method" name="default_shipping_method">
                                            <option value="">Selecione</option>
                                            <option value="flat_rate" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'flat_rate' ? 'selected' : '' }}>Valor fixo</option>
                                            <option value="free_shipping" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'free_shipping' ? 'selected' : '' }}>Frete gratis</option>
                                            <option value="weight_based" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'weight_based' ? 'selected' : '' }}>Baseado em peso</option>
                                            <option value="correios" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'correios' ? 'selected' : '' }}>Correios</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="estimated_delivery_days">Prazo medio de entrega</label>
                                        <input type="number" class="form-control" id="estimated_delivery_days"
                                               name="estimated_delivery_days"
                                               value="{{ $shippingSettings['estimated_delivery_days'] ?? '' }}">
                                        <small class="form-text text-muted">Quantidade media de dias quando o metodo nao retornar prazo proprio.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="free_shipping_threshold">Valor minimo para frete gratis</label>
                                        <input type="number" step="0.01" class="form-control" id="free_shipping_threshold"
                                               name="free_shipping_threshold"
                                               value="{{ $shippingSettings['free_shipping_threshold'] ?? '' }}">
                                        <small class="form-text text-muted">Pedidos acima deste valor podem receber frete gratis.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="flat_rate_shipping">Valor do frete fixo</label>
                                        <input type="number" step="0.01" class="form-control" id="flat_rate_shipping"
                                               name="flat_rate_shipping"
                                               value="{{ $shippingSettings['flat_rate_shipping'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h4 class="mb-3">Correios</h4>
                            <div class="alert alert-info">
                                A API oficial de preco e prazo dos Correios exige token e liberacao conforme contrato ou permissao no CWS. Sem token, a loja usa as regras internas de frete configuradas no sistema.
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="hidden" name="correios_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" id="correios_enabled" name="correios_enabled" value="1"
                                            {{ (bool) ($shippingSettings['correios_enabled'] ?? config('shipping.correios.enabled')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="correios_enabled">Ativar cotacao dos Correios</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mb-3">
                                        <input type="hidden" name="correios_contract_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" id="correios_contract_enabled" name="correios_contract_enabled" value="1"
                                            {{ (bool) ($shippingSettings['correios_contract_enabled'] ?? config('shipping.correios.contract_enabled')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="correios_contract_enabled">Usar dados de contrato</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_origin_cep">CEP de origem</label>
                                        <input type="text" class="form-control" id="correios_origin_cep" name="correios_origin_cep" maxlength="9"
                                               value="{{ $shippingSettings['correios_origin_cep'] ?? config('shipping.correios.origin_cep') }}"
                                               placeholder="00000-000">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_contract_number">Numero do contrato</label>
                                        <input type="text" class="form-control" id="correios_contract_number" name="correios_contract_number"
                                               value="{{ $shippingSettings['correios_contract_number'] ?? config('shipping.correios.contract_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="correios_regional_code">Codigo DR</label>
                                        <input type="text" class="form-control" id="correios_regional_code" name="correios_regional_code"
                                               value="{{ $shippingSettings['correios_regional_code'] ?? config('shipping.correios.regional_code') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="correios_service_codes">Servicos</label>
                                        <input type="text" class="form-control" id="correios_service_codes" name="correios_service_codes"
                                               value="{{ $shippingSettings['correios_service_codes'] ?? config('shipping.correios.service_codes') }}"
                                               placeholder="03220:SEDEX,03298:PAC">
                                        <small class="form-text text-muted">Formato: codigo:nome separado por virgula.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="correios_access_token">Token Bearer dos Correios</label>
                                <textarea class="form-control" id="correios_access_token" name="correios_access_token" rows="3"
                                          placeholder="Informe o token do CWS ou deixe vazio para usar CORREIOS_ACCESS_TOKEN no .env">{{ $shippingSettings['correios_access_token'] ?? config('shipping.correios.access_token') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correios_preco_base_url">URL base da API de preco</label>
                                        <input type="url" class="form-control" id="correios_preco_base_url" name="correios_preco_base_url"
                                               value="{{ $shippingSettings['correios_preco_base_url'] ?? config('shipping.correios.preco_base_url') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correios_prazo_base_url">URL base da API de prazo</label>
                                        <input type="url" class="form-control" id="correios_prazo_base_url" name="correios_prazo_base_url"
                                               value="{{ $shippingSettings['correios_prazo_base_url'] ?? config('shipping.correios.prazo_base_url') }}">
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-3">Medidas padrao do pacote</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_default_weight_grams">Peso padrao (gramas)</label>
                                        <input type="number" class="form-control" id="correios_default_weight_grams" name="correios_default_weight_grams"
                                               value="{{ $shippingSettings['correios_default_weight_grams'] ?? config('shipping.correios.default_weight_grams') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_default_length_cm">Comprimento (cm)</label>
                                        <input type="number" class="form-control" id="correios_default_length_cm" name="correios_default_length_cm"
                                               value="{{ $shippingSettings['correios_default_length_cm'] ?? config('shipping.correios.default_length_cm') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_default_width_cm">Largura (cm)</label>
                                        <input type="number" class="form-control" id="correios_default_width_cm" name="correios_default_width_cm"
                                               value="{{ $shippingSettings['correios_default_width_cm'] ?? config('shipping.correios.default_width_cm') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correios_default_height_cm">Altura (cm)</label>
                                        <input type="number" class="form-control" id="correios_default_height_cm" name="correios_default_height_cm"
                                               value="{{ $shippingSettings['correios_default_height_cm'] ?? config('shipping.correios.default_height_cm') }}">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Salvar configuracoes de frete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cep = document.getElementById('correios_origin_cep');

            if (!cep) {
                return;
            }

            cep.addEventListener('input', function () {
                const digits = cep.value.replace(/\D/g, '').slice(0, 8);
                cep.value = digits.length > 5 ? digits.slice(0, 5) + '-' + digits.slice(5) : digits;
            });
        });
    </script>
@endpush
