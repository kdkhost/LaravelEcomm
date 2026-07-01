@extends('auth.layout')

@section('title', 'Recuperar senha')

@section('content')
    <h1>Recuperar senha</h1>
    <p class="rataplam-auth-subtitle">Informe seu e-mail e enviaremos as instruções para criar uma nova senha.</p>

    @if (session('status'))
        <div class="rataplam-auth-alert" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form class="rataplam-auth-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">E-mail cadastrado</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="rataplam-auth-button">Enviar link de recuperação</button>
    </form>

    <div class="rataplam-auth-links">
        <a href="{{ route('login') }}">Voltar para o login</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Criar uma conta</a>
        @endif
    </div>
@endsection
