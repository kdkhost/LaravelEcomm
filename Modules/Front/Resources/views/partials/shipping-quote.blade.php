@php
    $shippingContext = $context ?? 'cart';
    $shippingProductSlug = $productSlug ?? null;
@endphp

<div class="shipping-quote-box" data-shipping-quote>
    <h4>Calcule o frete</h4>
    <form class="shipping-quote-form" action="{{ route('front.shipping.quote') }}" method="POST" data-context="{{ $shippingContext }}">
        @csrf
        <input type="hidden" name="context" value="{{ $shippingContext }}">
        @if($shippingProductSlug)
            <input type="hidden" name="product_slug" value="{{ $shippingProductSlug }}">
        @endif
        <input type="hidden" name="quantity" value="1" data-shipping-quantity>

        <div class="shipping-quote-row">
            <input type="text" name="postal_code" inputmode="numeric" autocomplete="postal-code" maxlength="9" placeholder="00000-000" aria-label="CEP para calculo de frete">
            <button type="submit" class="btn">Calcular</button>
        </div>
        <div class="shipping-quote-feedback" aria-live="polite"></div>
        <div class="shipping-quote-results"></div>
    </form>
</div>

@once
    @push('styles')
        <style>
            .shipping-quote-box {
                margin: 18px 0;
                padding: 16px;
                border: 1px solid #e5e7eb;
                border-radius: 6px;
                background: #fff;
            }

            .shipping-quote-box h4 {
                margin: 0 0 12px;
                font-size: 18px;
                font-weight: 600;
            }

            .shipping-quote-row {
                display: flex;
                gap: 8px;
                align-items: stretch;
            }

            .shipping-quote-row input {
                flex: 1;
                min-width: 0;
                height: 42px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 0 12px;
            }

            .shipping-quote-row .btn {
                height: 42px;
                white-space: nowrap;
            }

            .shipping-quote-feedback {
                margin-top: 10px;
                font-size: 14px;
                color: #6b7280;
            }

            .shipping-quote-results {
                margin-top: 12px;
            }

            .shipping-quote-method {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                padding: 10px 0;
                border-top: 1px solid #f1f1f1;
            }

            .shipping-quote-method strong,
            .shipping-quote-method span {
                display: block;
            }

            .shipping-quote-method small {
                display: block;
                color: #6b7280;
                margin-top: 2px;
            }

            @media (max-width: 480px) {
                .shipping-quote-row {
                    flex-direction: column;
                }

                .shipping-quote-row .btn {
                    width: 100%;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-shipping-quote]').forEach(function (box) {
                    const form = box.querySelector('.shipping-quote-form');
                    const cepInput = form.querySelector('input[name="postal_code"]');
                    const quantityInput = form.querySelector('[data-shipping-quantity]');
                    const feedback = box.querySelector('.shipping-quote-feedback');
                    const results = box.querySelector('.shipping-quote-results');

                    cepInput.addEventListener('input', function () {
                        const digits = cepInput.value.replace(/\D/g, '').slice(0, 8);
                        cepInput.value = digits.length > 5 ? digits.slice(0, 5) + '-' + digits.slice(5) : digits;
                    });

                    form.addEventListener('submit', function (event) {
                        event.preventDefault();

                        const pageQuantity = document.querySelector('input[name="quantity"], input[name="quantity[1]"], #quantity');
                        if (pageQuantity && quantityInput) {
                            quantityInput.value = pageQuantity.value || '1';
                        }

                        feedback.textContent = 'Calculando frete...';
                        results.innerHTML = '';

                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(form),
                        })
                            .then(function (response) {
                                return response.json().then(function (payload) {
                                    if (!response.ok) {
                                        throw payload;
                                    }

                                    return payload;
                                });
                            })
                            .then(function (payload) {
                                feedback.textContent = payload.message || 'Frete calculado.';

                                if (!payload.methods || payload.methods.length === 0) {
                                    return;
                                }

                                results.innerHTML = payload.methods.map(function (method) {
                                    return '<div class="shipping-quote-method">' +
                                        '<div><strong>' + escapeHtml(method.name || 'Frete') + '</strong>' +
                                        '<small>' + escapeHtml(method.provider || 'Loja') + ' - ' + escapeHtml(method.deadline || 'Prazo sob consulta') + '</small></div>' +
                                        '<span>' + escapeHtml(method.formatted_price || 'R$ 0,00') + '</span>' +
                                        '</div>';
                                }).join('');
                            })
                            .catch(function (payload) {
                                feedback.textContent = payload.message || 'Nao foi possivel calcular o frete para este CEP.';
                            });
                    });
                });

                function escapeHtml(value) {
                    return String(value).replace(/[&<>"']/g, function (char) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;'
                        }[char];
                    });
                }
            });
        </script>
    @endpush
@endonce
