<x-market-layout title="Painel de Gestão de Lojas — Cá do Bairro">
    <div class="container py-5" style="max-width: 1140px;">

        <!-- Header Section -->
        <div class="card position-relative shadow border-2 mb-5" style="border-color: var(--ink); border-radius: 20px; background-color: #ffffff;">
            <!-- Top striped banner -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px); border-top-left-radius: 18px; border-top-right-radius: 18px;"></div>

            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge-stamp mb-2">
                            <i class="fa-solid fa-store me-1"></i>Painel do Comerciante
                        </span>
                        <h1 class="profile-name mb-2" style="font-size: clamp(1.8rem, 3vw, 2.4rem);">
                            Gestão de <span style="color: var(--accent-1);">Lojas</span>
                        </h1>
                        <p class="text-muted mb-0" style="font-size: 1.05rem;">
                            Administre os seus estabelecimentos locais, dados do negócio e equipa de colaboradores.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                        <button type="button" class="btn-mkt-ghost text-decoration-none d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#joinStoreModal">
                            <i class="fa-solid fa-door-open"></i>
                            <span>Juntar-se com Código</span>
                        </button>
                        <a href="{{ route('stores.create') }}" class="btn-mkt-submit border-0 text-decoration-none d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span>Registar Nova Loja</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Status / Flash Messages -->
        @if (session('success'))
            <div class="auth-alert alert-success d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-check fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="auth-alert alert-danger d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
        @endif

        <!-- KPI Stats Row -->
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="info-label d-block mb-1">Total de Lojas</span>
                            <h2 class="fw-bold mb-0" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink); font-size: 2rem;">
                                {{ $stats['total'] }}
                            </h2>
                        </div>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; border-radius: 12px; background: var(--ink);">
                            <i class="fa-solid fa-store fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="info-label d-block mb-1">Lojas Próprias</span>
                            <h2 class="fw-bold mb-0 text-success" style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 2rem;">
                                {{ $stats['owned'] }}
                            </h2>
                        </div>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; border-radius: 12px; background: var(--accent-1);">
                            <i class="fa-solid fa-crown fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="info-label d-block mb-1">Colaborações</span>
                            <h2 class="fw-bold mb-0" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ochre); font-size: 2rem;">
                                {{ $stats['employed'] }}
                            </h2>
                        </div>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 52px; height: 52px; border-radius: 12px; background: var(--ochre);">
                            <i class="fa-solid fa-users fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Column: Stores List -->
            <div class="col-lg-8">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="heading-mkt mb-0">Os Seus Estabelecimentos</h3>
                    <span class="badge bg-light text-muted border px-3 py-2" style="font-size: 0.85rem;">
                        {{ $stores->count() }} {{ $stores->count() === 1 ? 'loja encontrada' : 'lojas encontradas' }}
                    </span>
                </div>

                @if ($stores->isEmpty())
                    <!-- Empty State -->
                    <div class="card p-5 text-center border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                        <div class="mb-3">
                            <i class="fa-solid fa-store-slash" style="font-size: 3.5rem; color: var(--muted); opacity: 0.5;"></i>
                        </div>
                        <h4 class="fw-bold mb-2" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink);">
                            Ainda não tens nenhuma loja associada
                        </h4>
                        <p class="text-muted mb-4 mx-auto" style="max-width: 440px;">
                            Registe o seu primeiro estabelecimento no bairro para começar a divulgar os seus produtos e serviços aos clientes locais.
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="{{ route('stores.join') }}" class="btn-mkt-ghost text-decoration-none px-4 py-2">
                                <i class="fa-solid fa-door-open me-2"></i>Tenho um Código de Convite
                            </a>
                            <a href="{{ route('stores.create') }}" class="btn-mkt-submit border-0 text-decoration-none px-4 py-2">
                                <i class="fa-solid fa-circle-plus me-2"></i>Registar a Minha Primeira Loja
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Full-Width Store Cards -->
                    <div class="d-flex flex-column gap-3">
                        @foreach ($stores as $store)
                            <div class="card border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                                <div class="card-body p-4">
                                    <!-- Store Header Row -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                        <div class="d-flex align-items-center gap-3 min-w-0">
                                            <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                                 style="width: 52px; height: 52px; border-radius: 12px; background-color: var(--accent-1); border: 2px solid var(--ink); font-size: 1.2rem;">
                                                {{ $store->initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    <h4 class="fw-bold mb-0 text-truncate" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink);">
                                                        {{ $store->name }}
                                                    </h4>
                                                    <span class="badge bg-success" style="font-size: 0.72rem;">Ativa</span>
                                                </div>
                                                <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                    @if ($store->pivot->role === 'owner')
                                                        <span class="badge bg-success" style="font-size: 0.72rem;">
                                                            <i class="fa-solid fa-crown me-1"></i>Proprietário
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                                            <i class="fa-solid fa-user-tie me-1"></i>Funcionário
                                                        </span>
                                                    @endif
                                                    <span class="text-muted small">
                                                        <i class="fa-regular fa-calendar-check me-1 text-success"></i>Registada em {{ $store->created_at ? $store->created_at->format('d/m/Y') : 'Data Indisponível' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Store Actions -->
                                        <div class="d-flex align-items-center gap-2 ms-auto">
                                            <a href="{{ route('stores.show', $store->id) }}" class="btn-mkt-ghost btn-sm text-decoration-none px-3">
                                                <i class="fa-regular fa-eye me-1"></i>Ver Loja
                                            </a>
                                            @if ($store->pivot->role === 'owner')
                                                <a href="{{ url('/gestao/lojas/' . $store->id) }}" class="btn-mkt-submit btn-sm text-decoration-none border-0 px-3">
                                                    <i class="fa-solid fa-gear me-1"></i>Gerir
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                                        {{ $store->description ?: 'Sem descrição do estabelecimento adicionada.' }}
                                    </p>

                                    <!-- Meta Info Row -->
                                    <div class="d-flex flex-wrap align-items-center gap-4 pt-3 border-top border-dashed text-muted small" style="border-color: var(--line) !important;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-location-dot text-danger flex-shrink-0"></i>
                                            <span class="text-truncate" style="max-width: 320px;">{{ $store->place_id ?: 'Coordenadas definidas' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-users text-mustard flex-shrink-0"></i>
                                            <span>{{ $store->users_count }} {{ $store->users_count === 1 ? 'colaborador associado' : 'colaboradores associados' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- User Profile Summary Card -->
                <div class="card p-4 mb-4 border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                             style="width: 56px; height: 56px; border-radius: 50%; background: var(--accent-1); border: 2px solid var(--ink); font-size: 1.3rem;">
                            {{ $user->initials }}
                        </div>
                        <div class="min-w-0">
                            <h5 class="fw-bold mb-0 text-truncate" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink);">
                                {{ $user->name }}
                            </h5>
                            <span class="user-type-badge py-0 px-2 mt-1 d-inline-block" style="font-size: 0.72rem;">
                                {{ $user->role_label }}
                            </span>
                        </div>
                    </div>

                    <div class="text-muted small mb-3 d-flex align-items-center gap-2">
                        <i class="fa-regular fa-envelope"></i>
                        <span class="text-truncate">{{ $user->email }}</span>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="btn-mkt-ghost btn-sm w-100 text-decoration-none text-center">
                        <i class="fa-regular fa-user me-1"></i>Gerir o Meu Perfil
                    </a>
                </div>

                <!-- Quick Actions Card (Directly below Profile Card) -->
                <div class="card p-4 border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <h5 class="heading-mkt mb-3">Navegação Rápida</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('stores.create') }}" class="btn-mkt-submit text-decoration-none text-center border-0 py-2">
                            <i class="fa-solid fa-circle-plus me-2"></i>Registar Nova Loja
                        </a>
                        <a href="{{ route('stores.join') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-door-open me-2"></i>Juntar a uma Loja
                        </a>
                        <a href="{{ route('explore') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-compass me-2"></i>Explorar Outras Lojas
                        </a>
                        <a href="{{ route('map') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-map-location-dot me-2"></i>Ver Mapa Geral
                        </a>
                        <a href="{{ route('help') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-regular fa-circle-question me-2"></i>Perguntas Frequentes
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Join Store Modal -->
    <div class="modal fade" id="joinStoreModal" tabindex="-1" aria-labelledby="joinStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2" style="border-color: var(--ink); border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="joinStoreModalLabel">
                        <i class="fa-solid fa-door-open me-2 text-mustard"></i>Juntar-se a uma Loja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" action="{{ route('stores.join.submit') }}">
                    @csrf
                    <div class="modal-body py-4">
                        <p class="text-muted small mb-3">
                            Introduza o código de convite de 7 carateres fornecido pelo proprietário da loja para se juntar à equipa como colaborador.
                        </p>
                        <div class="mb-3">
                            <label for="invite_code_input" class="form-label-stamp">Código de Convite <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="market-input text-center text-uppercase fw-bold font-monospace fs-4"
                                   id="invite_code_input"
                                   name="code"
                                   maxlength="7"
                                   placeholder="EX: A1B2C3D"
                                   required
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn-mkt-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-mkt-submit border-0 px-4">Juntar-se à Loja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-market-layout>
