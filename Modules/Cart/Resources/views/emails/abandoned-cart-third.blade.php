<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Última chance! Carrinho expira em breve</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .urgent-badge {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin: 20px 0;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .product-item {
            border: 1px solid #eee;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }
        .product-details {
            flex: 1;
        }
        .product-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .final-discount {
            background: #8e44ad;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            border: 2px dashed #fff;
        }
        .cta-button {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .cta-button:hover {
            background: #c0392b;
        }
        .countdown {
            background: #34495e;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .unsubscribe {
            margin-top: 20px;
        }
        .unsubscribe a {
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
            <div class="urgent-badge">⏰ ÚLTIMA CHANCE</div>
            <h1>Seu carrinho expira em breve!</h1>
        </div>

        <p>Olá {{ $userName }},</p>

        <p>Este é seu lembrete final! Seu carrinho expirará em breve e estes itens podem não estar mais disponíveis por este preço.</p>

        <div class="final-discount">
            🎉 OFERTA FINAL: Use o cupom <strong>{{ $discountCode }}</strong> e ganhe {{ $discountPercent }}% DE DESCONTO!
        </div>

        <div class="countdown">
            ⏰ Esta oferta expira em 24 horas
        </div>

        <h2>Seus Itens no Carrinho:</h2>
        @foreach($cartItems as $item)
            @if($item['product'])
            <div class="product-item">
                @if($item['product']->imageUrl)
                    <img src="{{ $item['product']->imageUrl }}" alt="{{ $item['product']->title }}" class="product-image">
                @endif
                <div class="product-details">
                    <div class="product-title">{{ $item['product']->title }}</div>
                    <div>Quantidade: {{ $item['quantity'] }}</div>
                    <div class="product-price">R$ {{ number_format($item['amount'], 2, ',', '.') }}</div>
                </div>
            </div>
            @endif
        @endforeach

        <div style="text-align: center; margin: 30px 0;">
            <strong>Total: R$ {{ number_format($abandonedCart->total_amount, 2, ',', '.') }}</strong><br>
            <span style="color: #27ae60; font-size: 18px;">Com desconto: R$ {{ number_format($abandonedCart->total_amount * (1 - $discountPercent/100), 2, ',', '.') }}</span><br>
            <span style="color: #e74c3c;">Você economiza: R$ {{ number_format($abandonedCart->total_amount * ($discountPercent/100), 2, ',', '.') }}!</span>
        </div>

        <div style="text-align: center;">
            <a href="{{ $cartUrl }}" class="cta-button">Finalizar Compra Agora</a>
        </div>

        <p><strong>Não perca!</strong> Esta é sua última chance de garantir estes itens com preço especial. Após este e-mail, seu carrinho será limpo.</p>

        <div class="footer">
            <p>Obrigado por escolher {{ config('app.name') }}!</p>
            <div class="unsubscribe">
                <a href="{{ $unsubscribeUrl }}">Cancelar inscrição destes e-mails</a>
            </div>
        </div>
    </div>
</body>
</html>
