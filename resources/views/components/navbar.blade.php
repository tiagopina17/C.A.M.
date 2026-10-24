<nav class="navbar navbar-expand-lg navbar-dark w-100 pt-3 ps-3 pb-3" style="background-color: var(--color-dark, #3B3B3B);">
    <div class="container-fluid">
        <a class="navbar-brand fs-2 sam" href="{{ url('/') }}">S.A.M</a>
        <button class="navbar-toggler text-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span> Menu
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item fs-5 fw-semibold ps-3">
                    <a href="{{ url('/') }}" class="nav-link textonav">Início</a>
                </li>
                <li class="nav-item fs-5 fw-semibold ps-3">
                    <a href="{{ url('/explorar') }}" class="nav-link textonav">Serviços</a>
                </li>
                <li class="nav-item fs-5 fw-semibold ps-3">
                    <a href="{{ url('/mapa') }}" class="nav-link textonav">Mapa</a>
                </li>
                <li class="nav-item fs-5 fw-semibold ps-3">
                    <a href="{{ url('/ajuda') }}" class="nav-link textonav">Ajuda</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto pe-3">
                @auth
                    <li class="nav-item dropdown fs-5 fw-semibold ps-3">
                        <a class="nav-link dropdown-toggle textonav d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user me-2"></i>
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                            @if(Auth::user()->ownedStores()->exists() || Auth::user()->employedStores()->exists())
                                <li><a class="dropdown-item" href="{{ url('/gestao/lojas') }}">Gerir Lojas</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Terminar Sessão</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item fs-5 fw-semibold ps-3">
                        <a href="{{ route('login') }}" class="nav-link textonav">Login</a>
                    </li>
                    <li class="nav-item fs-5 fw-semibold ps-3 d-flex align-items-center">
                        <a href="{{ route('register') }}" class="btn-pill-mustard fs-6 text-decoration-none">Registo</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

