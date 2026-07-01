<!DOCTYPE html>
<html>
<head>
    <title>Alerta de Estoque Baixo</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        p { font-size: 16px; color: #666; }
        ul { list-style-type: none; padding: 0; }
        li { background: #f9f9f9; margin: 5px 0; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .footer { margin-top: 20px; font-size: 14px; color: #999; }
    </style>
</head>
<body>
<div class="container">
    <h1>{{ config('app.name') }} - Alerta de Estoque Baixo</h1>
    <p>Os seguintes produtos estão com estoque abaixo de 10 unidades:</p>
    <ul>
        @foreach($products as $product)
            <li>{{ $product->title }}: {{ $product->stock }}</li>
        @endforeach
    </ul>
    <div class="footer">
        <p>Obrigado por usar {{ config('app.name') }}. Por favor, reabasteça os itens o mais rápido possível.</p>
    </div>
</div>
</body>
</html>
