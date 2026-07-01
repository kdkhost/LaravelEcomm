@extends('auth.layout')

@section('title', 'Confirmar senha')

@section('content')
    <h1>Confirmar senha</h1>
    <p class="rataplam-auth-subtitle">Confirme sua senha para continuar com segurança.</p>

    <form class="rataplam-auth-form" method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <label for="password">Senha atual</label>
            <input id="password"
                   type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password"
                   required
                   autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="rataplam-auth-button">Confirmar senha</button>
    </form>

    <div class="rataplam-auth-links">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Esqueci minha senha</a>
        @endif
    </div>
@endsection
