<x-market-layout title="Painel de Administração — Cá do Bairro">
    <div class="container py-5" style="max-width: 1140px;">

        <!-- Admin Header Card -->
        <div class="card position-relative shadow border-2 mb-5" style="border-color: var(--ink); border-radius: 20px; background-color: #ffffff;">
            <!-- Top striped banner -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px); border-top-left-radius: 18px; border-top-right-radius: 18px;"></div>

            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge-stamp mb-2">
                            <i class="fa-solid fa-screwdriver-wrench me-1"></i>Backoffice Administrativo
                        </span>
                        <h1 class="profile-name mb-2" style="font-size: clamp(1.8rem, 3vw, 2.4rem);">
                            Painel de <span style="color: var(--accent-1);">Administração</span>
                        </h1>
                        <p class="text-muted mb-0" style="font-size: 1.05rem;">
                            Supervisão global da plataforma, métricas de crescimento, utilizadores e catálogo do bairro.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <span class="badge bg-danger py-2 px-3 fw-bold shadow-sm" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-shield-halved me-1"></i>Acesso Restrito: Administrador
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Stats Cards Row -->
        <div class="row g-3 mb-5">
            <div class="col-md-6 col-xl-3">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="info-label mb-0">Utilizadores</span>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; border-radius: 10px; background: var(--ink);">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink); font-size: 2.2rem;">
                        {{ number_format($stats['total_users']) }}
                    </h2>
                    <small class="text-success fw-medium">
                        <i class="fa-solid fa-arrow-up me-1"></i>+{{ $stats['recent_users'] }} nos últimos 30 dias
                    </small>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="info-label mb-0">Total de Lojas</span>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; border-radius: 10px; background: var(--accent-1);">
                            <i class="fa-solid fa-store"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1 text-success" style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 2.2rem;">
                        {{ number_format($stats['total_stores']) }}
                    </h2>
                    <small class="text-muted">
                        Estabelecimentos locais
                    </small>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="info-label mb-0">Categorias</span>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; border-radius: 10px; background: var(--ochre);">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ochre); font-size: 2.2rem;">
                        {{ number_format($stats['total_categories']) }}
                    </h2>
                    <small class="text-muted">
                        Setores comerciais ativos
                    </small>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card p-3 p-md-4 border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="info-label mb-0">Serviços / Produtos</span>
                        <div class="d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; border-radius: 10px; background: #2b3a2f;">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; color: #2b3a2f; font-size: 2.2rem;">
                        {{ number_format($stats['total_services']) }}
                    </h2>
                    <small class="text-muted">
                        Itens catalogados
                    </small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- Recent Users Table (8 cols) -->
            <div class="col-lg-8">
                <div class="card border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <h4 class="heading-mkt mb-0">
                            <i class="fa-solid fa-users me-2 text-muted"></i>Utilizadores Recentes
                        </h4>
                        <span class="badge bg-light text-muted border">Últimos 10 registados</span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-muted font-monospace text-uppercase" style="letter-spacing: 0.05em;">
                                        <th scope="col" class="ps-4">Utilizador</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Lojas</th>
                                        <th scope="col" class="pe-4">Registo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentUsers as $recentUser)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                                         style="width: 38px; height: 38px; border-radius: 50%; background-color: var(--accent-1); font-size: 0.88rem;">
                                                        {{ $recentUser->initials }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="fw-bold text-dark text-truncate">{{ $recentUser->name }}</div>
                                                        <small class="text-muted text-truncate d-block">{{ $recentUser->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($recentUser->isAdmin())
                                                    <span class="badge bg-danger" style="font-size: 0.72rem;">Administrador</span>
                                                @elseif ($recentUser->ownedStores->count() > 0 || $recentUser->employedStores->count() > 0)
                                                    <span class="badge bg-success" style="font-size: 0.72rem;">Comerciante</span>
                                                @else
                                                    <span class="badge bg-secondary" style="font-size: 0.72rem;">Cliente</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="small fw-semibold text-dark">
                                                    {{ $recentUser->ownedStores->count() + $recentUser->employedStores->count() }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-muted small">
                                                {{ $recentUser->created_at ? $recentUser->created_at->format('d/m/Y H:i') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                Nenhum utilizador encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Breakdown (4 cols) -->
            <div class="col-lg-4">
                <div class="card border-2 shadow-sm h-100" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="card-header bg-white border-bottom p-4" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                        <h4 class="heading-mkt mb-0">
                            <i class="fa-solid fa-chart-pie me-2 text-muted"></i>Catálogo por Categorias
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        @if ($categories->isEmpty())
                            <p class="text-muted small mb-0 text-center py-4">Nenhuma categoria registada.</p>
                        @else
                            @php
                                $maxServices = max($categories->max('services_count'), 1);
                            @endphp
                            <div class="d-flex flex-column gap-3">
                                @foreach ($categories as $cat)
                                    @php
                                        $percent = min(round(($cat->services_count / $maxServices) * 100), 100);
                                    @endphp
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-semibold small text-dark">{{ $cat->name }}</span>
                                            <span class="badge bg-light text-dark border">{{ $cat->services_count }} serviços</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 999px; background-color: var(--paper);">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background-color: var(--accent-1); border-radius: 999px;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Stores Grid Section -->
        <div class="card border-2 shadow-sm mb-4" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between" style="border-top-left-radius: 14px; border-top-right-radius: 14px;">
                <h4 class="heading-mkt mb-0">
                    <i class="fa-solid fa-store me-2 text-muted"></i>Estabelecimentos Registados Recentemente
                </h4>
                <a href="{{ route('explore') }}" class="btn-mkt-ghost btn-sm text-decoration-none">
                    <i class="fa-solid fa-compass me-1"></i>Ver Todas no Mercado
                </a>
            </div>

            <div class="card-body p-4">
                @if ($recentStores->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">Nenhum estabelecimento registado na plataforma.</p>
                @else
                    <div class="row g-3">
                        @foreach ($recentStores as $recentStore)
                            <div class="col-md-6 col-lg-4">
                                <div class="p-3 border rounded-3 h-100 d-flex flex-column justify-content-between" style="background: var(--paper);">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0"
                                                 style="width: 40px; height: 40px; border-radius: 10px; background-color: var(--accent-1); font-size: 0.95rem;">
                                                {{ $recentStore->initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <h6 class="fw-bold mb-0 text-truncate text-dark">{{ $recentStore->name }}</h6>
                                                <small class="text-muted">{{ $recentStore->category->name ?? 'Geral' }}</small>
                                            </div>
                                        </div>

                                        <p class="text-muted small mb-2 text-truncate" style="font-size: 0.82rem;">
                                            {{ $recentStore->description ?: 'Sem descrição.' }}
                                        </p>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between pt-2 border-top border-dashed text-muted small" style="border-color: var(--line) !important; font-size: 0.78rem;">
                                        <span><i class="fa-solid fa-users me-1 text-mustard"></i>{{ $recentStore->users_count }} equipa</span>
                                        <a href="{{ route('stores.show', $recentStore->id) }}" class="text-success fw-bold text-decoration-none">
                                            Ver Loja <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-market-layout>

