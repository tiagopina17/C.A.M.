<x-market-layout title="Gerir Estabelecimento — {{ $store->name }}">
    <div class="container py-5" style="max-width: 1140px;">

        <!-- Header Card -->
        <div class="card position-relative shadow border-2 mb-5" style="border-color: var(--ink); border-radius: 20px; background-color: #ffffff;">
            <!-- Top striped banner -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px); border-top-left-radius: 18px; border-top-right-radius: 18px;"></div>

            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge-stamp mb-2">
                            <i class="fa-solid fa-gear me-1"></i>Gestão de Estabelecimento
                        </span>
                        <h1 class="profile-name mb-2" style="font-size: clamp(1.8rem, 3vw, 2.4rem);">
                            {{ $store->name }}
                        </h1>
                        <p class="text-muted mb-0" style="font-size: 1.05rem;">
                            Atualize os dados da sua loja e faça a gestão da sua equipa de colaboradores.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                        <a href="{{ route('management.stores.index') }}" class="btn-mkt-ghost text-decoration-none">
                            <i class="fa-solid fa-arrow-left me-1"></i>Voltar
                        </a>
                        <a href="{{ route('stores.show', $store->id) }}" class="btn-mkt-ghost text-decoration-none">
                            <i class="fa-regular fa-eye me-1"></i>Ver Loja
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

        @if ($errors->any())
            <div class="auth-alert alert-danger d-flex align-items-center gap-2 mb-4 shadow-sm">
                <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                <div class="fw-semibold">{{ $errors->first() }}</div>
            </div>
        @endif

        <div class="row g-4">
            <!-- Main Column: Store Details & Team -->
            <div class="col-lg-8">

                <!-- 1. Store Details Card -->
                <div class="card border-2 shadow-sm mb-4" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="heading-mkt mb-1">Informações do Estabelecimento</h3>
                        <p class="text-muted small mb-4">Modifique a identificação e descrição pública da loja.</p>

                        <form method="POST" action="{{ route('management.stores.update', $store->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label-stamp">Nome da Loja <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="market-input @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $store->name) }}"
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label-stamp">Descrição do Negócio</label>
                                <textarea class="market-input @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="3"
                                          placeholder="Apresente os seus produtos tradicionais ou história do estabelecimento...">{{ old('description', $store->description) }}</textarea>
                            </div>

                            <!-- Read-only Location Metadata -->
                            <div class="row g-3 p-3 rounded-3 mb-4" style="background: var(--paper); border: 2px dashed var(--line);">
                                <div class="col-md-6">
                                    <span class="info-label d-block mb-1">Place ID (Google Maps)</span>
                                    <span class="font-monospace small text-dark d-block text-truncate" title="{{ $store->place_id }}">
                                        {{ $store->place_id ?: 'Não associado' }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <span class="info-label d-block mb-1">Coordenadas Geográficas</span>
                                    <span class="font-monospace small text-dark d-block">
                                        {{ $store->lat }}, {{ $store->lon }}
                                    </span>
                                </div>
                                <div class="col-12">
                                    <span class="info-label d-block mb-1">Data de Abertura na Plataforma</span>
                                    <span class="small text-muted">
                                        <i class="fa-regular fa-calendar-check me-1 text-success"></i>
                                        {{ $store->created_at ? $store->created_at->format('d/m/Y \à\s H:i') : 'Data Indisponível' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-mkt-submit border-0 px-4">
                                    <i class="fa-regular fa-floppy-disk me-2"></i>Guardar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 2. Team & Employees Card -->
                <div class="card border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h3 class="heading-mkt mb-1">Equipa de Colaboradores</h3>
                                <p class="text-muted small mb-0">Gerencie quem tem acesso de funcionário a esta loja.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn-mkt-submit btn-sm border-0" data-bs-toggle="modal" data-bs-target="#inviteCodeModal">
                                    <i class="fa-solid fa-key me-1"></i>Gerar Código
                                </button>
                                <button type="button" class="btn-mkt-ghost btn-sm" data-bs-toggle="modal" data-bs-target="#activeCodesModal">
                                    <i class="fa-solid fa-list me-1"></i>Códigos ({{ $activeCodes->count() }})
                                </button>
                            </div>
                        </div>

                        <!-- Employee List Table -->
                        @if ($employees->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fa-solid fa-users-slash fs-1 mb-2 opacity-50"></i>
                                <p class="mb-0">Ainda não existem colaboradores associados a esta loja.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="small text-muted font-monospace text-uppercase" style="letter-spacing: 0.05em;">
                                            <th scope="col">Colaborador</th>
                                            <th scope="col">Papel</th>
                                            <th scope="col">Adicionado em</th>
                                            <th scope="col" class="text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                                             style="width: 36px; height: 36px; border-radius: 50%; background-color: var(--accent-1); font-size: 0.85rem;">
                                                            {{ $employee->initials }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark mb-0">
                                                                {{ $employee->name }}
                                                                @if ($employee->id === $user->id)
                                                                    <span class="badge bg-primary-subtle text-primary border ms-1" style="font-size: 0.65rem;">Você</span>
                                                                @endif
                                                            </div>
                                                            <small class="text-muted">{{ $employee->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($employee->pivot->role === 'owner')
                                                        <span class="badge bg-success" style="font-size: 0.72rem;">
                                                            <i class="fa-solid fa-crown me-1"></i>Proprietário
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary" style="font-size: 0.72rem;">
                                                            <i class="fa-solid fa-user-tie me-1"></i>Funcionário
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $employee->pivot->created_at ? $employee->pivot->created_at->format('d/m/Y') : 'Data Indisponível' }}
                                                </td>
                                                <td class="text-end">
                                                    @if ($employee->id !== $user->id)
                                                        <form method="POST" action="{{ route('management.stores.remove_employee', [$store->id, $employee->id]) }}" class="d-inline" onsubmit="return confirm('Tem a certeza que deseja remover este colaborador da loja?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-1" title="Remover da loja">
                                                                <i class="fa-solid fa-user-minus me-1"></i>Remover
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Store Quick Summary Card -->
                <div class="card p-4 mb-4 border-2 shadow-sm text-center" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-center text-white fw-bold shadow-sm mx-auto mb-3"
                         style="width: 72px; height: 72px; border-radius: 16px; background: var(--accent-1); border: 2px solid var(--ink); font-size: 1.6rem;">
                        {{ $store->initials }}
                    </div>

                    <h4 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; color: var(--ink);">
                        {{ $store->name }}
                    </h4>

                    <span class="badge bg-success-subtle text-success border border-success mb-3 py-1 px-3">
                        <i class="fa-solid fa-circle-check me-1"></i>Estabelecimento Ativo
                    </span>

                    <a href="{{ route('stores.show', $store->id) }}" class="btn-mkt-ghost btn-sm w-100 text-decoration-none">
                        <i class="fa-regular fa-eye me-1"></i>Ver Loja Pública
                    </a>
                </div>

                <!-- Store Stats Card -->
                <div class="card p-4 mb-4 border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <h5 class="heading-mkt mb-3">Estatísticas da Loja</h5>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-dashed">
                            <span class="text-muted small">Total de Colaboradores</span>
                            <span class="fw-bold text-dark fs-6">{{ $employees->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-dashed">
                            <span class="text-muted small">Códigos Ativos</span>
                            <span class="fw-bold text-mustard fs-6">{{ $activeCodes->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Papel Atual</span>
                            <span class="badge bg-success">Proprietário</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Card -->
                <div class="card p-4 border-2 shadow-sm" style="border-color: var(--ink); border-radius: 16px; background: #ffffff;">
                    <h5 class="heading-mkt mb-3">Navegação Rápida</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('management.stores.index') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-solid fa-arrow-left me-2"></i>Voltar ao Painel
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-regular fa-user me-2"></i>O Meu Perfil
                        </a>
                        <a href="{{ route('help') }}" class="btn-mkt-ghost text-decoration-none text-center">
                            <i class="fa-regular fa-circle-question me-2"></i>Centro de Ajuda
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal 1: Generate Invite Code Modal -->
    <div class="modal fade" id="inviteCodeModal" tabindex="-1" aria-labelledby="inviteCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2" style="border-color: var(--ink); border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="inviteCodeModalLabel">
                        <i class="fa-solid fa-key me-2 text-mustard"></i>Gerar Código de Convite
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <p class="text-muted small mb-4">
                        Gere um código de convite único de 7 carateres. O colaborador poderá introduzi-lo para se juntar à equipa desta loja. Cada código é válido por <strong>7 dias</strong>.
                    </p>

                    <!-- Initial State -->
                    <div id="gen-initial-state">
                        <button type="button" class="btn-mkt-submit border-0 px-4 py-2" id="trigger-gen-btn">
                            <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Gerar Novo Código
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="gen-loading-state" style="display: none;">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="text-muted small mt-2">A gerar código único...</p>
                    </div>

                    <!-- Success Display State -->
                    <div id="gen-result-state" style="display: none;">
                        <div class="p-3 rounded-3 mb-3 border-2" style="background-color: var(--paper); border: 2px dashed var(--accent-1);">
                            <span class="info-label d-block mb-1">Código de Convite</span>
                            <span id="display-invite-code" class="fw-bold font-monospace text-dark" style="font-size: 2.2rem; letter-spacing: 0.15em;"></span>
                        </div>

                        <div class="alert alert-warning py-2 px-3 small d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="fa-solid fa-clock"></i>
                            <span>Expira em: <strong id="display-invite-expiry"></strong></span>
                        </div>

                        <button type="button" class="btn-mkt-ghost btn-sm px-4" id="copy-code-btn">
                            <i class="fa-regular fa-copy me-1"></i><span id="copy-btn-text">Copiar Código</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: Active Codes Modal -->
    <div class="modal fade" id="activeCodesModal" tabindex="-1" aria-labelledby="activeCodesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2" style="border-color: var(--ink); border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="activeCodesModalLabel">
                        <i class="fa-solid fa-list me-2 text-mustard"></i>Códigos de Convite Ativos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body py-4">
                    @if ($activeCodes->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-key fs-1 mb-2 opacity-50"></i>
                            <p class="mb-0">Não existem códigos de convite ativos no momento.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-2" id="active-codes-list">
                            @foreach ($activeCodes as $activeCode)
                                <div class="p-3 rounded-3 d-flex align-items-center justify-content-between border" style="background: var(--paper);">
                                    <div>
                                        <span class="font-monospace fw-bold fs-5 text-dark" style="letter-spacing: 0.1em;">
                                            {{ $activeCode->code }}
                                        </span>
                                        <small class="text-muted d-block">
                                            Expira: {{ $activeCode->expires_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-pill copy-active-code-btn" data-code="{{ $activeCode->code }}">
                                        <i class="fa-regular fa-copy me-1"></i>Copiar
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const triggerBtn = document.getElementById('trigger-gen-btn');
            const initialState = document.getElementById('gen-initial-state');
            const loadingState = document.getElementById('gen-loading-state');
            const resultState = document.getElementById('gen-result-state');
            const codeDisplay = document.getElementById('display-invite-code');
            const expiryDisplay = document.getElementById('display-invite-expiry');
            const copyBtn = document.getElementById('copy-code-btn');
            const copyText = document.getElementById('copy-btn-text');

            function copyTextToClipboard(text, onSuccess) {
                if (!text) return;
                text = text.trim();

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(() => {
                        if (onSuccess) onSuccess();
                    }).catch(() => {
                        execCommandCopy(text, onSuccess);
                    });
                } else {
                    execCommandCopy(text, onSuccess);
                }
            }

            function execCommandCopy(text, onSuccess) {
                try {
                    const tempInput = document.createElement('textarea');
                    tempInput.value = text;
                    tempInput.setAttribute('readonly', '');
                    tempInput.style.position = 'fixed';
                    tempInput.style.left = '-9999px';
                    tempInput.style.top = '-9999px';
                    tempInput.style.opacity = '0';
                    document.body.appendChild(tempInput);
                    tempInput.focus();
                    tempInput.select();
                    const successful = document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    if (successful && onSuccess) {
                        onSuccess();
                    } else {
                        prompt('Copie o código manualmente:', text);
                    }
                } catch (err) {
                    prompt('Copie o código manualmente:', text);
                }
            }

            if (triggerBtn) {
                triggerBtn.addEventListener('click', function() {
                    initialState.style.display = 'none';
                    loadingState.style.display = 'block';

                    fetch('{{ route('management.stores.generate_code', $store->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        loadingState.style.display = 'none';
                        if (data.success) {
                            codeDisplay.textContent = data.code;
                            expiryDisplay.textContent = data.expires_at;
                            if (copyBtn) copyBtn.setAttribute('data-code', data.code);
                            resultState.style.display = 'block';
                        } else {
                            alert('Erro ao gerar código: ' + (data.error || 'Erro desconhecido.'));
                            initialState.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        loadingState.style.display = 'none';
                        initialState.style.display = 'block';
                        alert('Erro de rede ao gerar código.');
                    });
                });
            }

            if (copyBtn) {
                copyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-code') || codeDisplay.textContent.trim();
                    if (!code) return;

                    copyTextToClipboard(code, function() {
                        const icon = copyBtn.querySelector('i');
                        if (icon) {
                            icon.className = 'fa-solid fa-check me-1 text-success';
                        }
                        if (copyText) copyText.textContent = 'Copiado!';
                        copyBtn.classList.add('border-success');

                        setTimeout(() => {
                            if (icon) {
                                icon.className = 'fa-regular fa-copy me-1';
                            }
                            if (copyText) copyText.textContent = 'Copiar Código';
                            copyBtn.classList.remove('border-success');
                        }, 2500);
                    });
                });
            }

            document.querySelectorAll('.copy-active-code-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = this.getAttribute('data-code');
                    if (!code) return;

                    copyTextToClipboard(code, function() {
                        const originalHTML = btn.innerHTML;
                        btn.innerHTML = '<i class="fa-solid fa-check me-1 text-success"></i>Copiado!';
                        btn.classList.add('border-success');
                        setTimeout(() => {
                            btn.innerHTML = originalHTML;
                            btn.classList.remove('border-success');
                        }, 2500);
                    });
                });
            });
        });
    </script>
    @endpush
</x-market-layout>

