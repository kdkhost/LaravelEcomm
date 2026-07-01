<!DOCTYPE html>
<html lang="{{ app()->getLocale() ?? 'pt' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>404 - Página não encontrada | Rataplam</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 40px 20px;
            max-width: 600px;
            width: 100%;
        }
        .error-code {
            font-size: 160px;
            font-weight: 900;
            color: #F7941D;
            line-height: 1;
            text-shadow: 4px 4px 0 rgba(247,148,29,0.15);
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d2d2d;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            color: #666;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            background: #F7941D;
            color: #fff;
            padding: 14px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-home:hover {
            background: #e07e00;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(247,148,29,0.3);
            color: #fff;
        }
        .btn-home i {
            margin-right: 8px;
        }
        .error-icon {
            font-size: 48px;
            color: #F7941D;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        @media (max-width: 576px) {
            .error-code { font-size: 100px; }
            .error-title { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fa fa-frown-o"></i>
        </div>
        <div class="error-code">404</div>
        <div class="error-title">Página não encontrada</div>
        <div class="error-message">
            A página que você procura pode ter sido removida,<br>
            renomeada ou está temporariamente indisponível.
        </div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fa fa-home"></i> Voltar para a página inicial
        </a>
    </div>
</body>
</html>
