@php
    $themePath = 'front::themes.modern';
@endphp

@extends($themePath . '.layouts.master')

@section('title', 'Provador virtual')

@section('content')
    <div class="breadcrumb-container">
        <div class="container">
            <ol class="breadcrumb">
                <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Inicio</a></li>
                <li class="active">Provador virtual</li>
            </ol>
        </div>
    </div>

    <section class="main-container">
        <div class="container">
            <div class="row">
                <div class="main col-md-6">
                    <div style="position: relative; min-height: 420px;">
                        @if($product)
                            <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" style="width: 100%; max-height: 520px; object-fit: contain;">
                        @else
                            <div class="text-center" style="background: #f6f7f9; min-height: 420px; padding: 80px 30px;">
                                <h3>Provador virtual</h3>
                                <p>Selecione um produto para visualizar o caimento estimado.</p>
                            </div>
                        @endif
                        <div id="fit-overlay" style="position: absolute; left: 50%; top: 18%; width: 34%; height: 58%; transform: translateX(-50%); border: 2px dashed #f7941d; border-radius: 16px; background: rgba(247,148,29,.08); display: none;"></div>
                    </div>
                </div>

                <aside class="col-md-6">
                    <h1 class="page-title">{{ $product?->title ?? 'Provador virtual' }}</h1>
                    <div class="separator-2"></div>
                    <p>Informe suas medidas em centimetros para receber uma recomendacao de tamanho.</p>

                    <form id="virtual-try-on-form" action="{{ route('front.virtual-try-on.recommend') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Altura (cm)</label>
                                    <input type="number" name="height" class="form-control" min="80" max="230" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Torax/Busto (cm)</label>
                                    <input type="number" name="chest" class="form-control" min="40" max="180" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Cintura (cm)</label>
                                    <input type="number" name="waist" class="form-control" min="35" max="180" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Quadril (cm)</label>
                                    <input type="number" name="hip" class="form-control" min="40" max="190" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-default">Calcular tamanho</button>
                        @if($product)
                            <a href="{{ route('front.product-detail', $product->slug) }}" class="btn btn-gray">Voltar ao produto</a>
                        @endif
                    </form>

                    <div id="try-on-result" class="alert alert-success" style="display: none; margin-top: 20px;"></div>

                    <h3 class="title">Tabela de referencia</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Tamanho</th>
                                <th>Torax/Busto</th>
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
                </aside>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('virtual-try-on-form')?.addEventListener('submit', async function (event) {
            event.preventDefault();

            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.querySelector('[name=_token]').value,
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            });

            const data = await response.json();
            const result = document.getElementById('try-on-result');
            result.style.display = 'block';
            result.innerHTML = '<strong>' + data.message + '</strong><br>' + data.fit;
            document.getElementById('fit-overlay').style.display = 'block';
        });
    </script>
@endpush
