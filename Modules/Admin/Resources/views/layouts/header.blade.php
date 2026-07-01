<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav align-items-center">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="sidebarToggleTop" aria-label="Alternar menu">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <form class="form-inline ml-2 d-none d-md-flex admin-header-search">
        <div class="input-group input-group-sm">
            <input type="text" class="form-control form-control-navbar" placeholder="Buscar no painel" aria-label="Buscar no painel">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="button" aria-label="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('front.index') }}" target="_blank" title="Abrir loja">
                <i class="fas fa-store"></i>
            </a>
        </li>

        <li class="nav-item dropdown no-arrow mx-1" id="messageT" data-url="{{ route('messages.five') }}">
            @include('message::message')
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" href="#" data-toggle="dropdown" aria-expanded="false">
                <span class="admin-locale-badge">{{ mb_strtoupper(app()->getLocale()) }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                <a class="dropdown-item" href="{{ route('language.switch', 'pt') }}">Portugues (Brasil)</a>
                <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a>
                <a class="dropdown-item" href="{{ route('language.switch', 'de') }}">Deutsch</a>
            </div>
        </li>

        <li class="nav-item dropdown user-menu">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                @php $user = Auth()->user(); @endphp
                @if($user && $user->getFirstMediaUrl('photo'))
                    <img class="user-image img-circle elevation-2" src="{{ $user->getFirstMediaUrl('photo') }}" alt="Avatar">
                @else
                    <img class="user-image img-circle elevation-2" src="{{ asset('backend/img/avatar.png') }}" alt="Avatar">
                @endif
                <span class="d-none d-md-inline ml-2">{{ Auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-sm" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ route('user-profile') }}">
                    <i class="fas fa-user mr-2 text-muted"></i> Perfil
                </a>
                <a class="dropdown-item" href="{{ route('front.index') }}" target="_blank">
                    <i class="fas fa-external-link-alt mr-2 text-muted"></i> Ver loja
                </a>
                @auth
                    @if(session('impersonated_by'))
                        <a class="dropdown-item" href="{{ route('users.leave-impersonate') }}">
                            <i class="fas fa-user-secret mr-2 text-muted"></i> Sair da impersonacao
                        </a>
                    @endif
                @endauth
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
