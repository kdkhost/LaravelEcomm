@extends('auth.layout')

@section('title', 'Redefinir senha')

@section('content')
    <h1>Redefinir senha</h1>
    <p class="rataplam-auth-subtitle">Crie uma nova senha para recuperar o acesso à sua conta.</p>

    <form class="rataplam-auth-form" method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email"
                   type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ $email ?? old('email') }}"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Nova senha</label>
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
            <label for="password-confirm">Confirmar nova senha</label>
            <input id="password-confirm"
                   type="password"
                   class="form-control"
                   name="password_confirmation"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="rataplam-auth-button">Salvar nova senha</button>
    </form>

    <div class="rataplam-auth-links">
        <a href="{{ route('login') }}">Voltar para o login</a>
    </div>
@endsection
