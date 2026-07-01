<nav class="app-header navbar navbar-expand bg-body" data-admin-header>
    <div class="container-fluid">
        <ul class="navbar-nav align-items-center gap-1">
            <li class="nav-item">
                <a class="nav-link admin-icon-button" href="#" data-lte-toggle="sidebar" role="button" aria-label="Alternar menu">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <form class="admin-header-search ms-2">
                    <div class="input-group input-group-sm">
                        <input type="search" class="form-control form-control-navbar" placeholder="Buscar no painel" aria-label="Buscar no painel">
                        <button class="btn btn-navbar" type="button" aria-label="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center gap-1">
            <li class="nav-item">
                <a class="nav-link admin-icon-button" href="{{ route('front.index') }}" target="_blank" title="Abrir loja">
                    <i class="fas fa-store"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link admin-icon-button" href="#" data-lte-toggle="fullscreen" role="button" title="Tela cheia">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>

            <li class="nav-item">
                <button type="button" class="nav-link btn btn-link admin-theme-toggle admin-icon-button" id="adminThemeToggle" aria-label="Alternar tema">
                    <i class="fas fa-moon"></i>
                </button>
            </li>

            <li class="nav-item dropdown" id="messageT" data-url="{{ route('messages.five') }}">
                @include('message::message')
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link admin-icon-button" href="#" data-bs-toggle="dropdown" aria-expanded="false" title="Idioma">
                    <span class="admin-locale-badge">{{ mb_strtoupper(app()->getLocale()) }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow-sm">
                    <a class="dropdown-item" href="{{ route('language.switch', 'pt') }}">Portugues (Brasil)</a>
                    <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a>
                    <a class="dropdown-item" href="{{ route('language.switch', 'de') }}">Deutsch</a>
                </div>
            </li>

            <li class="nav-item dropdown user-menu">
                <a class="nav-link d-flex align-items-center admin-user-menu-trigger" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @php $user = Auth()->user(); @endphp
                    @if($user && $user->getFirstMediaUrl('photo'))
                        <img class="user-image rounded-circle shadow-sm" src="{{ $user->getFirstMediaUrl('photo') }}" alt="Avatar">
                    @else
                        <img class="user-image rounded-circle shadow-sm" src="{{ asset('backend/img/avatar.png') }}" alt="Avatar">
                    @endif
                    <span class="d-none d-md-inline ms-2 admin-user-name">{{ $user?->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow-sm">
                    <div class="dropdown-header text-start">
                        <strong>{{ $user?->name }}</strong>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('user-profile') }}">
                        <i class="fas fa-user me-2 text-muted"></i> Perfil
                    </a>
                    <a class="dropdown-item" href="{{ route('front.index') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-2 text-muted"></i> Ver loja
                    </a>
                    @auth
                        @if(session('impersonated_by'))
                            <a class="dropdown-item" href="{{ route('users.leave-impersonate') }}">
                                <i class="fas fa-user-secret me-2 text-muted"></i> Sair da impersonacao
                            </a>
                        @endif
                    @endauth
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i> Sair
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
