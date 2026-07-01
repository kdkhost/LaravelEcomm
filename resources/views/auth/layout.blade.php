<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acesso') - {{ config('app.name', 'Rataplam') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('frontend/img/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/all_front.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/themify-icons.css') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body.rataplam-auth {
            min-height: 100vh;
            margin: 0;
            font-family: Poppins, Arial, sans-serif;
            color: #20242c;
            background: #f4f7fb;
        }

        .rataplam-auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(320px, 520px) 1fr;
        }

        .rataplam-auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px 28px;
            background: #ffffff;
        }

        .rataplam-auth-card {
            width: 100%;
            max-width: 420px;
        }

        .rataplam-auth-logo {
            display: inline-flex;
            align-items: center;
            margin-bottom: 28px;
        }

        .rataplam-auth-logo img {
            max-height: 58px;
            width: auto;
        }

        .rataplam-auth-card h1 {
            margin: 0 0 10px;
            font-size: 28px;
            line-height: 1.25;
            font-weight: 700;
            color: #141820;
        }

        .rataplam-auth-subtitle {
            margin: 0 0 26px;
            color: #667085;
            line-height: 1.6;
        }

        .rataplam-auth-form .form-group {
            margin-bottom: 16px;
        }

        .rataplam-auth-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333946;
        }

        .rataplam-auth-form .form-control {
            width: 100%;
            height: 48px;
            border: 1px solid #d8dee8;
            border-radius: 8px;
            padding: 0 14px;
            box-shadow: none;
            color: #20242c;
            background: #ffffff;
        }

        .rataplam-auth-form .form-control:focus {
            border-color: #09afdf;
            box-shadow: 0 0 0 3px rgba(9, 175, 223, 0.14);
            outline: 0;
        }

        .rataplam-auth-form .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #586174;
        }

        .rataplam-auth-button {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 8px;
            background: #09afdf;
            color: #ffffff;
            font-weight: 700;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .rataplam-auth-button:hover,
        .rataplam-auth-button:focus {
            background: #078fba;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(9, 175, 223, 0.22);
        }

        .rataplam-auth-links {
            display: grid;
            gap: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .rataplam-auth-links a {
            color: #078fba;
            font-weight: 600;
        }

        .rataplam-auth-alert {
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 18px;
            background: #eaf8ee;
            color: #256a3a;
        }

        .invalid-feedback,
        .rataplam-auth-error {
            display: block;
            margin-top: 6px;
            color: #c0392b;
            font-size: 13px;
        }

        .is-invalid {
            border-color: #c0392b !important;
        }

        .rataplam-auth-visual {
            position: relative;
            display: flex;
            align-items: flex-end;
            min-height: 100vh;
            padding: 56px;
            color: #ffffff;
            background:
                linear-gradient(135deg, rgba(8, 29, 43, 0.78), rgba(9, 175, 223, 0.52)),
                url("{{ asset('frontend/img/logo2.png') }}") center 42% / 260px auto no-repeat,
                #1c2733;
        }

        .rataplam-auth-visual-content {
            max-width: 520px;
        }

        .rataplam-auth-visual h2 {
            margin: 0 0 12px;
            font-size: 36px;
            line-height: 1.2;
            color: #ffffff;
        }

        .rataplam-auth-visual p {
            margin: 0;
            font-size: 16px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.86);
        }

        @media (max-width: 900px) {
            .rataplam-auth-shell {
                grid-template-columns: 1fr;
            }

            .rataplam-auth-visual {
                display: none;
            }

            .rataplam-auth-panel {
                min-height: 100vh;
                padding: 30px 18px;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="rataplam-auth">
<main class="rataplam-auth-shell">
    <section class="rataplam-auth-panel">
        <div class="rataplam-auth-card">
            <a href="{{ route('front.index') }}" class="rataplam-auth-logo">
                <img src="{{ asset('frontend/img/logo.png') }}" alt="Rataplam">
            </a>

            @yield('content')
        </div>
    </section>
    <aside class="rataplam-auth-visual" aria-hidden="true">
        <div class="rataplam-auth-visual-content">
            <h2>Rataplam</h2>
            <p>Moda infantil com navegação segura, compra simples e acesso organizado para acompanhar seus pedidos.</p>
        </div>
    </aside>
</main>
@stack('scripts')
</body>
</html>
