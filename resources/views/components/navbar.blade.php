<nav class="navbar navbar-expand-lg navbar-dark market-navbar shadow-sm">
    <div class="container position-relative">
        <a class="navbar-brand brand-bairro" href="{{ url('/') }}">Cá do <span class="brand-accent">Bairro</span></a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-center mb-2 mb-lg-0 gap-lg-2 gap-xl-3 align-items-lg-center">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Início</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/explorar') }}" class="nav-link {{ request()->is('explorar*') ? 'active' : '' }}">Lojas</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/mapa') }}" class="nav-link {{ request()->is('mapa*') ? 'active' : '' }}">Mapa</a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/ajuda') }}" class="nav-link {{ request()->is('ajuda*') ? 'active' : '' }}">Ajuda</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-2 gap-lg-3">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user fs-5 text-mustard"></i>
                            <span class="fw-medium">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-2" style="border-color: var(--ink); background-color: var(--card); border-radius: 12px;" aria-labelledby="navbarDropdown">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Sessão iniciada como</small>
                                <strong class="d-block text-truncate" style="max-width: 200px;">{{ Auth::user()->email }}</strong>
                            </li>
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-regular fa-user me-2 text-muted"></i>Perfil</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('stores.create') }}"><i class="fa-solid fa-circle-plus me-2 text-muted"></i>Registar Loja</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('stores.join') }}"><i class="fa-solid fa-door-open me-2 text-muted"></i>Juntar a uma Loja</a></li>
                            <li><a class="dropdown-item py-2" href="{{ route('management.stores.index') }}"><i class="fa-solid fa-store me-2 text-muted"></i>Painel de Lojas</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Terminar Sessão</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="{{ route('register') }}" class="btn-pill-mustard text-decoration-none d-inline-block">Registo</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
