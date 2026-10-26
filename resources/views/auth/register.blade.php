<x-market-layout title="Criar Conta — Cá do Bairro">
    <div class="auth-page-wrapper">
        <div class="auth-card position-relative overflow-hidden shadow">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px);"></div>

            <div class="text-center pt-2">
                <span class="badge-stamp">
                    <i class="fa-solid fa-user-plus"></i>Selo · nova conta
                </span>
            </div>

            <h1 class="auth-title">Criar <span style="color: var(--accent-1);">Conta</span></h1>
            <p class="auth-subtitle">Preencha o formulário abaixo para se registar na plataforma.</p>

            <!-- Errors Alert (Single error only) -->
            @if ($errors->any())
                <div class="auth-alert alert-danger mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold" style="font-size: 0.95rem;">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label-stamp">Nome Completo</label>
                    <input type="text"
                           class="market-input @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Ex.: Maria Santos"
                           required
                           autofocus
                           autocomplete="name">
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label-stamp">Endereço de Email</label>
                    <input type="email"
                           class="market-input @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="maria@exemplo.pt"
                           required
                           autocomplete="username">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label-stamp">Palavra-passe</label>
                    <div class="input-group-mkt" style="background-color: #ffffff; border: 2px solid var(--ink); border-radius: 12px; overflow: hidden;">
                        <input type="password"
                               class="form-control-mkt-flat"
                               id="password"
                               name="password"
                               placeholder="Mínimo 8 carateres (com número e símbolo)"
                               required
                               autocomplete="new-password"
                               style="padding: 12px 16px; font-family: 'Inter', sans-serif; font-size: 0.98rem; color: #182b20;">
                        <button type="button" class="toggle-pw-btn" id="togglePassword" aria-label="Mostrar ou ocultar palavra-passe" style="background: transparent; border-left: 2px solid var(--ink); padding: 0 16px; color: var(--muted); cursor: pointer;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label-stamp">Confirmar Palavra-passe</label>
                    <div class="input-group-mkt" style="background-color: #ffffff; border: 2px solid var(--ink); border-radius: 12px; overflow: hidden;">
                        <input type="password"
                               class="form-control-mkt-flat"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Repita a sua palavra-passe"
                               required
                               autocomplete="new-password"
                               style="padding: 12px 16px; font-family: 'Inter', sans-serif; font-size: 0.98rem; color: #182b20;">
                        <button type="button" class="toggle-pw-btn" id="togglePasswordConfirm" aria-label="Mostrar ou ocultar confirmação da palavra-passe" style="background: transparent; border-left: 2px solid var(--ink); padding: 0 16px; color: var(--muted); cursor: pointer;">
                            <i class="fa-solid fa-eye" id="eyeIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn-mkt-submit w-100 border-0">
                        Criar Conta
                    </button>
                </div>
            </form>

            <div class="auth-link-line pt-3 mt-3 border-top text-center" style="border-color: rgba(24, 43, 32, 0.12) !important; font-size: 1rem;">
                <span style="color: #44594c;">Já tem uma conta?</span>
                <a href="{{ route('login') }}" class="auth-link ms-1 fw-bold" style="font-size: 1rem;">Iniciar Sessão</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupPasswordToggle(buttonId, inputId, iconId) {
                const btn = document.getElementById(buttonId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (btn && input && icon) {
                    btn.addEventListener('click', function () {
                        const isPassword = input.getAttribute('type') === 'password';
                        input.setAttribute('type', isPassword ? 'text' : 'password');

                        if (isPassword) {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                }
            }

            setupPasswordToggle('togglePassword', 'password', 'eyeIcon');
            setupPasswordToggle('togglePasswordConfirm', 'password_confirmation', 'eyeIconConfirm');
        });
    </script>
    @endpush
</x-market-layout>
