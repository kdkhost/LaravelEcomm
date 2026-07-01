<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclamação Criada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: #007bff;
            color: #ffffff;
            text-align: center;
            padding: 15px 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .content .highlight {
            font-weight: bold;
            color: #007bff;
        }

        .footer {
            background: #f8f9fa;
            text-align: center;
            padding: 15px 20px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>Reclamação Criada</h1>
    </div>
    <div class="content">
        <p><span class="highlight">ID da Reclamação:</span> {{ $complaint->id }}</p>
        <p><span class="highlight">Descrição:</span> {!! $complaint->description !!}</p>
        <p><span class="highlight">Status:</span> {{ ucfirst($complaint->status) }}</p>

        @if($recipientType === 'admin')
            <p><span class="highlight">ID do Pedido:</span> {{ $complaint->order_id }}</p>
            <p><span class="highlight">Criado por:</span> {{ $complaint->user->name }}</p>
        @endif
    </div>
    <div class="footer">
        <p>Obrigado por usar o <span class="highlight">{{ config('app.name') }}</span>.</p>
    </div>
</div>
</body>
</html>
