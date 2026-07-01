@extends('auth.layout')

@section('title', 'Criar conta')

@section('content')
    <h1>Criar conta</h1>
    <p class="rataplam-auth-subtitle">Cadastre seus dados para comprar com mais agilidade e acompanhar seus pedidos.</p>

    <form class="rataplam-auth-form" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nome completo</label>
            <input id="name"
                   type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   name="name"
                   value="{{ old('name') }}"
                   required
                   autocomplete="name"
                   autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email"
                   type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Senha</label>
            <input id="password"
                   type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password"
                   required
                   autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirmar senha</label>
            <input id="password-confirm"
                   type="password"
                   class="form-control"
                   name="password_confirmation"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="rataplam-auth-button">Criar conta</button>
    </form>

    <div class="rataplam-auth-links">
        <a href="{{ route('login') }}">Já tenho uma conta</a>
    </div>
@endsection
