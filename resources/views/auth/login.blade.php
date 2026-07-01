@extends('auth.layout')

@section('title', 'Entrar')

@section('content')
    <h1>Entrar na conta</h1>
    <p class="rataplam-auth-subtitle">Acesse sua conta para acompanhar pedidos, favoritos e dados de compra.</p>

    @if (session('status'))
        <div class="rataplam-auth-alert" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form class="rataplam-auth-form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">E-mail</label>
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

        <div class="form-group">
            <label for="password">Senha</label>
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   required
                   autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-check" for="remember">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Lembrar meu acesso</span>
            </label>
        </div>

        <button type="submit" class="rataplam-auth-button">Entrar</button>
    </form>

    <div class="rataplam-auth-links">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Esqueci minha senha</a>
        @endif
        <a href="{{ route('magic-login.show-login-form') }}">Entrar por link mágico</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Criar uma conta</a>
        @endif
    </div>
@endsection
