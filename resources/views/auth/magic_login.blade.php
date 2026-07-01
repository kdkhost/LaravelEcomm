@extends('auth.layout')

@section('title', 'Link mágico')

@section('content')
    <h1>Entrar por link mágico</h1>
    <p class="rataplam-auth-subtitle">Receba um link de acesso no e-mail cadastrado para entrar sem digitar senha.</p>

    @if(session('magic_link_sent'))
        <div class="rataplam-auth-alert" role="alert">
            {{ session('magic_link_sent') }}
        </div>
    @endif

    <form class="rataplam-auth-form" method="POST" action="{{ route('magic.send') }}">
        @csrf

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}"
                   id="email"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <button type="submit" class="rataplam-auth-button">Enviar link de acesso</button>
    </form>

    <div class="rataplam-auth-links">
        <a href="{{ route('login') }}">Entrar com senha</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}">Criar uma conta</a>
        @endif
    </div>
@endsection
