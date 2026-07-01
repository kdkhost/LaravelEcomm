@extends('front::layouts.master')
@section('title', 'Comparação de produtos')
@section('content')
    <div class="container my-4">
        <h2>Comparação de produtos</h2>
        @if($tooMany)
            <div class="alert alert-warning">Você pode comparar até 4 produtos. O item mais antigo foi removido.</div>
        @endif
        @if($products->isEmpty())
            <div class="alert alert-info">Nenhum produto selecionado para comparação.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                    <tr>
                        <th>Característica</th>
                        @foreach($products as $product)
                            <th>{{ $product->title }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Imagem</td>
                        @foreach($products as $product)
                            <td>
                                @if(method_exists($product, 'getFirstMediaUrl'))
                                    <img src="{{ $product->getFirstMediaUrl('images', 'thumb') ?? asset('img/no-image.png') }}"
                                         alt="{{ $product->title }}" style="max-width:100px;">
                                @else
                                    <span>Sem imagem</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Preço</td>
                        @foreach($products as $product)
                            <td>{{ format_currency((float) $product->price) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Descrição curta</td>
                        @foreach($products as $product)
                            <td>{{ Str::limit($product->summary, 100) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>Atributos</td>
                        @foreach($products as $product)
                            <td>
                                @if($product->attributeValues && $product->attributeValues->count())
                                    <ul class="list-unstyled mb-0">
                                        @foreach($product->attributeValues as $attrVal)
                                            @if($attrVal->attribute)
                                                <li><strong>{{ $attrVal->attribute->name }}:</strong> {{ $attrVal->value ?? '-' }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            </div>
        @endif
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Voltar</a>
    </div>
@endsection
