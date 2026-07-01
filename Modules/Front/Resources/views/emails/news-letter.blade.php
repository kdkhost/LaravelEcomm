<h1>E-mail de Verificação da Newsletter</h1>

<p>Por favor, verifique seu e-mail clicando no link abaixo:</p>
<a href="{{ route('validation', $token) }}">Verificar E-mail</a>

<p><a href="{{ route('delete-newsletter', $token) }}">Cancelar inscrição</a></p>

<p>Atenciosamente,<br>
{{ config('app.name') }}</p>
