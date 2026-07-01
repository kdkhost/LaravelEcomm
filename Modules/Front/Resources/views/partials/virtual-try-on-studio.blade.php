@php
    $imageOptions = collect($productImages ?? [])->values();
    $productTitle = $product?->title ?? 'Provador virtual';
    $primaryImage = data_get($imageOptions->first(), 'url', asset('frontend/img/logo.png'));
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('frontend/css/rataplam-virtual-try-on.css') }}?v=20260701ia">
    @endpush
    @push('scripts')
        <script src="{{ asset('frontend/js/rataplam-virtual-try-on.js') }}?v=20260701ia"></script>
    @endpush
@endonce

<section class="rataplam-tryon-studio"
         data-product-title="{{ e($productTitle) }}"
         data-primary-image="{{ e($primaryImage) }}"
         data-product-slug="{{ e($product?->slug ?? '') }}"
         data-recommend-url="{{ route('front.virtual-try-on.recommend') }}"
         data-ai-status-url="{{ route('front.virtual-try-on.status') }}"
         data-ai-process-url="{{ route('front.virtual-try-on.process') }}"
         data-csrf-token="{{ csrf_token() }}">
    <script type="application/json" class="rataplam-tryon-images">
        {!! $imageOptions->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <div class="rataplam-tryon-head">
        <div>
            <span class="rataplam-tryon-kicker">Provador virtual com IA</span>
            <h1>{{ $productTitle }}</h1>
            <p>Envie uma foto de corpo inteiro e gere uma imagem realista da crianca vestindo a peca escolhida com inteligencia artificial.</p>
        </div>
        @if($product)
            <a href="{{ route('front.product-detail', $product->slug) }}" class="rataplam-tryon-back">
                <i class="fa fa-arrow-left"></i> Voltar ao produto
            </a>
        @endif
    </div>

    <div class="rataplam-tryon-grid">
        <div class="rataplam-tryon-stage-panel">
            <div class="rataplam-tryon-toolbar">
                <button type="button" class="rataplam-tryon-tool is-active" data-tryon-view="front">Previa frente</button>
                <button type="button" class="rataplam-tryon-tool" data-tryon-view="side">Previa lateral</button>
                <button type="button" class="rataplam-tryon-tool" data-tryon-view="back">Previa costas</button>
                <button type="button" class="rataplam-tryon-tool" data-tryon-autoplay>Previa 360</button>
            </div>

            <div class="rataplam-tryon-canvas-wrap">
                <canvas class="rataplam-tryon-canvas" width="900" height="1200" aria-label="Visualizacao do provador virtual"></canvas>
                <div class="rataplam-tryon-empty">
                    <i class="fa fa-child"></i>
                    <strong>Foto de corpo inteiro</strong>
                    <span>Use uma foto em pe, com boa luz e corpo visivel.</span>
                </div>
            </div>

            <div class="rataplam-tryon-angle">
                <label for="rataplam-tryon-angle">Angulo 360</label>
                <input id="rataplam-tryon-angle" type="range" min="0" max="359" value="0" data-tryon-angle>
                <span data-tryon-angle-label>0 graus</span>
            </div>

            <div class="rataplam-tryon-frames" data-tryon-frames></div>
        </div>

        <aside class="rataplam-tryon-controls">
            @if(($products ?? collect())->count() > 0)
                <div class="rataplam-tryon-control-group">
                    <label for="rataplam-tryon-product">Produto</label>
                    <select id="rataplam-tryon-product" class="form-control" onchange="if (this.value) window.location.href = this.value;">
                        <option value="{{ route('front.virtual-try-on') }}" @selected(! $product)>Escolha uma peca</option>
                        @foreach($products as $item)
                            <option value="{{ route('front.virtual-try-on', ['slug' => $item->slug]) }}" @selected($product?->id === $item->id)>
                                {{ $item->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="rataplam-tryon-upload" data-tryon-dropzone>
                <input type="file" accept="image/jpeg,image/png,image/webp" data-tryon-photo>
                <i class="fa fa-upload"></i>
                <strong>Enviar foto</strong>
                <span>JPG, PNG ou WEBP ate 8 MB</span>
            </div>

            <label class="rataplam-tryon-consent">
                <input type="checkbox" data-tryon-consent>
                <span>Tenho autorizacao para usar esta foto no provador virtual.</span>
            </label>

            <div class="rataplam-tryon-ai-box">
                <div class="rataplam-tryon-ai-head">
                    <div>
                        <span>Resultado IA real</span>
                        <strong>Replicate FLUX Kontext Pro</strong>
                    </div>
                    <i class="fa fa-bolt"></i>
                </div>
                <div class="rataplam-tryon-ai-status" data-tryon-ai-status>
                    Verificando configuracao da IA...
                </div>
                <div class="rataplam-tryon-control-group">
                    <label for="rataplam-tryon-style">Estilo da imagem</label>
                    <select id="rataplam-tryon-style" class="form-control" data-tryon-style>
                        <option value="realista">Realista</option>
                        <option value="editorial">Editorial</option>
                        <option value="casual">Casual</option>
                    </select>
                </div>
                <button type="button"
                        class="rataplam-tryon-primary rataplam-tryon-ai-generate"
                        data-tryon-ai-generate
                        @disabled(! $product)>
                    <i class="fa fa-magic"></i> Gerar imagem com IA
                </button>
                <div class="rataplam-tryon-ai-result" data-tryon-ai-result></div>
            </div>

            @if($imageOptions->isNotEmpty())
                <div class="rataplam-tryon-control-group">
                    <label>Peca aplicada</label>
                    <div class="rataplam-tryon-garments">
                        @foreach($imageOptions as $index => $image)
                            <button type="button"
                                    class="rataplam-tryon-garment @if($index === 0) is-active @endif"
                                    data-tryon-garment="{{ e(data_get($image, 'url')) }}"
                                    title="{{ e(data_get($image, 'name', $productTitle)) }}">
                                <img src="{{ data_get($image, 'thumb') }}" alt="{{ e(data_get($image, 'name', $productTitle)) }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <form class="rataplam-tryon-measures" action="{{ route('front.virtual-try-on.recommend') }}" method="POST" data-tryon-measures>
                @csrf
                <input type="hidden" name="product_slug" value="{{ $product?->slug }}">
                <div class="rataplam-tryon-control-title">Medidas da crianca</div>
                <div class="rataplam-tryon-measure-grid">
                    <label>
                        Altura
                        <input type="number" name="height" min="80" max="230" value="120" required>
                    </label>
                    <label>
                        Torax
                        <input type="number" name="chest" min="40" max="180" value="64" required>
                    </label>
                    <label>
                        Cintura
                        <input type="number" name="waist" min="35" max="180" value="58" required>
                    </label>
                    <label>
                        Quadril
                        <input type="number" name="hip" min="40" max="190" value="68" required>
                    </label>
                    <label>
                        Ombro
                        <input type="number" name="shoulder" min="20" max="80" value="32">
                    </label>
                </div>
                <button type="submit" class="rataplam-tryon-primary">
                    <i class="fa fa-magic"></i> Calcular caimento
                </button>
            </form>

            <div class="rataplam-tryon-result" data-tryon-result></div>

            <div class="rataplam-tryon-control-group">
                <div class="rataplam-tryon-control-title">Ajustes finos</div>
                <label class="rataplam-tryon-range">
                    Largura da roupa
                    <input type="range" min="60" max="150" value="100" data-tryon-scale-x>
                </label>
                <label class="rataplam-tryon-range">
                    Altura da roupa
                    <input type="range" min="60" max="150" value="100" data-tryon-scale-y>
                </label>
                <label class="rataplam-tryon-range">
                    Posicao vertical
                    <input type="range" min="-120" max="160" value="0" data-tryon-offset-y>
                </label>
                <label class="rataplam-tryon-range">
                    Opacidade
                    <input type="range" min="45" max="100" value="92" data-tryon-opacity>
                </label>
            </div>

            <div class="rataplam-tryon-actions">
                <button type="button" class="rataplam-tryon-secondary" data-tryon-build-frames>
                    <i class="fa fa-refresh"></i> Gerar previa 360
                </button>
                <button type="button" class="rataplam-tryon-secondary" data-tryon-download>
                    <i class="fa fa-download"></i> Baixar previa
                </button>
            </div>

            <div class="rataplam-tryon-table">
                <strong>Tabela de referencia</strong>
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>Tam.</th>
                            <th>Torax</th>
                            <th>Cintura</th>
                            <th>Quadril</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sizes as $label => $size)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $size['chest'] }}</td>
                                <td>{{ $size['waist'] }}</td>
                                <td>{{ $size['hip'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </aside>
    </div>
</section>
