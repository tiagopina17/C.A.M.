<x-market-layout title="O Meu Perfil — Cá do Bairro">
    <div class="container py-5" style="max-width: 1140px;">

        <!-- Profile Hero Header Card -->
        <div class="card position-relative shadow border-2 mb-5" style="border-color: var(--ink); border-radius: 20px; background-color: #ffffff;">
            <!-- Top striped banner -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px); border-top-left-radius: 18px; border-top-right-radius: 18px;"></div>

            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <!-- Avatar Column -->
                    <div class="col-auto text-center">
                        <div class="profile-avatar shadow-sm mx-auto">
                            <span>{{ $user->initials }}</span>
                        </div>
                    </div>

                    <!-- User Info Column -->
                    <div class="col">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <h1 class="profile-name mb-0" style="font-size: clamp(1.6rem, 2.8vw, 2.2rem);">{{ $user->name }}</h1>
                            <span class="user-type-badge shadow-sm">
                                <i class="fa-solid fa-shield me-1"></i>{{ $user->role_label }}
                            </span>
                        </div>

                        <p class="text-muted mb-2 d-flex align-items-center gap-2">
                            <i class="fa-regular fa-envelope"></i>
                            <span class="fw-medium text-dark">{{ $user->email }}</span>
                        </p>

                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                            <span>
                                <i class="fa-regular fa-calendar-check me-1 text-success"></i>
                                Membro desde {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'Data Indisponível' }}
                            </span>
                            <span>·</span>
                            <span>
                                <i class="fa-solid fa-store me-1 text-mustard"></i>
                                {{ $user->stores->count() }} {{ $user->stores->count() === 1 ? 'Loja Associada' : 'Lojas Associadas' }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick CTA Column -->
                    <div class="col-12 col-md-auto text-md-end">
                        <a href="{{ route('stores.create') }}" class="btn-mkt-submit text-decoration-none d-inline-flex align-items-center gap-2 border-0">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span>Registar Loja</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Status Alerts -->
        @if (session('status') === 'profile-updated')
            <div class="auth-alert alert-success d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-check fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">Informações do perfil atualizadas com sucesso!</div>
            </div>
        @elseif (session('status') === 'password-updated')
            <div class="auth-alert alert-success d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-check fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">Password alterada com sucesso!</div>
            </div>
        @endif

        <!-- Global Error Alerts -->
        @if ($errors->any())
            <div class="auth-alert alert-danger d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">
                    {{ $errors->first() }}
                </div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4 gap-2 border-bottom-0" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-3 py-2" id="info-tab" data-bs-toggle="tab" data-bs-target="#tab-info" type="button" role="tab" aria-controls="tab-info" aria-selected="true">
                            <i class="fa-regular fa-id-card me-2"></i>Informações Pessoais
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2" id="security-tab" data-bs-toggle="tab" data-bs-target="#tab-security" type="button" role="tab" aria-controls="tab-security" aria-selected="false">
                            <i class="fa-solid fa-shield-halved me-2"></i>Segurança
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-3 py-2" id="stores-tab" data-bs-toggle="tab" data-bs-target="#tab-stores" type="button" role="tab" aria-controls="tab-stores" aria-selected="false">
                            <i class="fa-solid fa-store me-2"></i>Lojas <span class="badge bg-secondary ms-1">{{ $user->stores->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-danger px-3 py-2" id="danger-tab" data-bs-toggle="tab" data-bs-target="#tab-danger" type="button" role="tab" aria-controls="tab-danger" aria-selected="false">
                            <i class="fa-solid fa-trash-can me-2"></i>Conta
                        </button>
                    </li>
                </ul>

                <!-- Tab Contents -->
                <div class="tab-content" id="profileTabsContent">

                    <!-- Tab 1: Informações Pessoais -->
                    <div class="tab-pane fade show active" id="tab-info" role="tabpanel" aria-labelledby="info-tab">
                        <div class="card p-4 p-md-5 shadow-sm border-2" style="border-color: var(--ink); border-radius: 16px; background-color: #ffffff;">
                            <h3 class="heading-mkt mb-1">Dados da Conta</h3>
                            <p class="text-muted small mb-4">Mantenha o seu nome e endereço de correio eletrónico atualizados.</p>

                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('patch')

                                <div class="mb-3">
                                    <label for="name" class="form-label-stamp">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="market-input @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $user->name) }}"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label-stamp">Endereço de Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="market-input @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           required>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-mkt-submit border-0 px-4">
                                        <i class="fa-regular fa-floppy-disk me-2"></i>Guardar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tab 2: Segurança (Alterar Password) -->
                    <div class="tab-pane fade" id="tab-security" role="tabpanel" aria-labelledby="security-tab">
                        <div class="card p-4 p-md-5 shadow-sm border-2" style="border-color: var(--ink); border-radius: 16px; background-color: #ffffff;">
                            <h3 class="heading-mkt mb-1">Segurança e Credenciais</h3>
                            <p class="text-muted small mb-4">Altere a sua password regularmente para proteger a sua conta.</p>

                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf
                                @method('put')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label-stamp">Password Atual <span class="text-danger">*</span></label>
                                    <div class="input-group-mkt">
                                        <input type="password"
                                               class="form-control-mkt-flat @error('current_password', 'updatePassword') is-invalid @enderror"
                                               id="current_password"
                                               name="current_password"
                                               placeholder="A sua password atual"
                                               required>
                                        <button type="button" class="toggle-pw-btn px-3" data-target="current_password" aria-label="Mostrar password">
                                            <i class="fa-regular fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label-stamp">Nova Password <span class="text-danger">*</span></label>
                                    <div class="input-group-mkt">
                                        <input type="password"
                                               class="form-control-mkt-flat @error('password', 'updatePassword') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Mínimo de 8 carateres"
                                               required>
                                        <button type="button" class="toggle-pw-btn px-3" data-target="password" aria-label="Mostrar password">
                                            <i class="fa-regular fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Deve conter pelo menos 8 caracteres.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label-stamp">Confirmar Nova Password <span class="text-danger">*</span></label>
                                    <div class="input-group-mkt">
                                        <input type="password"
                                               class="form-control-mkt-flat @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Repita a nova password"
                                               required>
                                        <button type="button" class="toggle-pw-btn px-3" data-target="password_confirmation" aria-label="Mostrar password">
                                            <i class="fa-regular fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn-mkt-submit border-0 px-4">
                                        <i class="fa-solid fa-key me-2"></i>Atualizar Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tab 3: Lojas Associadas -->
                    <div class="tab-pane fade" id="tab-stores" role="tabpanel" aria-labelledby="stores-tab">
                        <div class="card p-4 p-md-5 shadow-sm border-2" style="border-color: var(--ink); border-radius: 16px; background-color: #ffffff;">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                                <div>
                                    <h3 class="heading-mkt mb-1">Lojas Associadas</h3>
                                    <p class="text-muted small mb-0">Estabelecimentos onde é proprietário ou funcionário.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('stores.join') }}" class="btn-mkt-ghost btn-sm text-decoration-none">
                                        <i class="fa-solid fa-door-open me-1"></i>Juntar com Código
                                    </a>
                                    <a href="{{ route('stores.create') }}" class="btn-mkt-submit btn-sm text-decoration-none border-0">
                                        <i class="fa-solid fa-plus me-1"></i>Nova Loja
                                    </a>
                                </div>
                            </div>

                            @if ($user->stores->isEmpty())
                                <div class="text-center py-5">
                                    <div class="mb-3 text-muted">
                                        <i class="fa-solid fa-store-slash" style="font-size: 3rem; opacity: 0.5;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Ainda não tens nenhuma loja associada</h5>
                                    <p class="text-muted mb-4" style="max-width: 440px; margin: 0 auto;">
                                        Regista o teu negócio no Cá do Bairro ou introduz um código de convite fornecido pelo proprietário de um estabelecimento.
                                    </p>
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <a href="{{ route('stores.join') }}" class="btn-mkt-ghost text-decoration-none px-4">
                                            <i class="fa-solid fa-door-open me-2"></i>Tenho um Código de Convite
                                        </a>
                                        <a href="{{ route('stores.create') }}" class="btn-mkt-submit text-decoration-none border-0 px-4">
                                            <i class="fa-solid fa-store me-2"></i>Registar a Minha Loja
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($user->stores as $store)
                                        <div class="p-3 border-2 rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="border: 2px solid var(--line); background-color: var(--paper);">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" style="width: 46px; height: 46px; border-radius: 10px; background-color: var(--accent-1); border: 2px solid var(--ink); font-size: 1.1rem;">
                                                    {{ mb_substr(preg_replace('/^\[.*?\]\s*/', '', $store->name), 0, 2) }}
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-dark">{{ $store->name }}</h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if ($store->pivot->role === 'owner')
                                                            <span class="badge bg-success" style="font-size: 0.72rem;">Proprietário</span>
                                                        @else
                                                            <span class="badge bg-secondary" style="font-size: 0.72rem;">Funcionário</span>
                                                        @endif
                                                        <small class="text-muted">{{ $store->category->name ?? 'Geral' }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ route('stores.show', $store->id) }}" class="btn-mkt-ghost btn-sm text-decoration-none">
                                                    <i class="fa-regular fa-eye me-1"></i>Ver
                                                </a>
                                                @if ($store->pivot->role === 'owner')
                                                    <a href="{{ url('/gestao/lojas/' . $store->id) }}" class="btn-mkt-submit btn-sm text-decoration-none border-0">
                                                        <i class="fa-solid fa-gear me-1"></i>Gerir
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tab 4: Zona de Risco (Eliminar Conta) -->
                    <div class="tab-pane fade" id="tab-danger" role="tabpanel" aria-labelledby="danger-tab">
                        <div class="card p-4 p-md-5 shadow-sm border-2" style="border-color: #b3411f; border-radius: 16px; background-color: #fff8f6;">
                            <div class="d-flex align-items-center gap-2 text-danger mb-2">
                                <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                                <h3 class="heading-mkt text-danger mb-0">Eliminar Conta</h3>
                            </div>
                            <p class="text-muted small mb-4">
                                Uma vez eliminada a sua conta, todos os recursos associados serão permanentemente apagados. Por favor, certifique-se de que pretende avançar antes de prosseguir.
                            </p>

                            <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                                <i class="fa-regular fa-trash-can me-2"></i>Eliminar a Minha Conta
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Statistics Card -->
                <div class="help-dark-cta p-4 mb-4 shadow">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="cta-icon-badge">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white fw-bold" style="font-family: 'Bricolage Grotesque', sans-serif;">Resumo da Conta</h4>
                            <small style="color: rgba(245, 242, 230, 0.7);">Estatísticas da tua atividade</small>
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mb-3" style="background: rgba(245, 242, 230, 0.08); border: 1px solid rgba(245, 242, 230, 0.15);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small">Total de Lojas</span>
                            <span class="fw-bold fs-5 text-white">{{ $user->stores->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50 small">Lojas Próprias</span>
                            <span class="fw-bold fs-5 text-mustard">{{ $user->ownedStores->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white-50 small">Colaborações</span>
                            <span class="fw-bold fs-5 text-white-50">{{ $user->employedStores->count() }}</span>
                        </div>
                    </div>

                    <p class="small mb-0" style="color: rgba(245, 242, 230, 0.65);">
                        <i class="fa-solid fa-circle-info me-1 text-mustard"></i>
                        Conta ativa e verificada no ecossistema Cá do Bairro.
                    </p>
                </div>

                <!-- Quick Actions Card -->
                <div class="card p-4 shadow-sm border-2" style="border-color: var(--ink); border-radius: 16px; background-color: #ffffff;">
                    <h5 class="heading-mkt mb-3">Ações Rápidas</h5>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('stores.create') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-circle-plus me-2"></i>Registar Nova Loja
                        </a>

                        <a href="{{ route('stores.join') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-door-open me-2"></i>Juntar a uma Loja
                        </a>

                        @if ($user->ownedStores()->exists() || $user->employedStores()->exists())
                            <a href="{{ url('/gestao/lojas') }}" class="btn-mkt-submit text-decoration-none text-center border-0">
                                <i class="fa-solid fa-store me-2"></i>Painel de Gestão
                            </a>
                        @endif

                        <a href="{{ url('/ajuda') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-regular fa-circle-question me-2"></i>Centro de Ajuda
                        </a>

                        <hr class="my-2">

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2 fw-semibold">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Terminar Sessão
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Confirm Deletion Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2" style="border-color: var(--ink); border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="confirmUserDeletionModalLabel">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Tem a certeza que deseja eliminar?
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Esta ação é irreversível. Por favor, introduza a sua password atual para confirmar que pretende apagar a sua conta permanentemente.
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}" id="delete-user-form">
                        @csrf
                        @method('delete')

                        <div class="mb-3">
                            <label for="deletion_password" class="form-label-stamp">Password de Confirmação <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="market-input @error('password', 'userDeletion') is-invalid @enderror"
                                   id="deletion_password"
                                   name="password"
                                   placeholder="A sua password"
                                   required>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <button type="button" class="btn-mkt-ghost" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 py-2 fw-bold">Eliminar Conta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            document.querySelectorAll('.toggle-pw-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (!input || !icon) return;

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
        });
    </script>
    @endpush
</x-market-layout>
