<x-market-layout title="Iniciar Sessão — Cá do Bairro">
    <div class="auth-page-wrapper">
        <div class="auth-card position-relative overflow-hidden shadow">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px);"></div>

            <div class="text-center pt-2">
                <span class="badge-stamp">
                    <i class="fa-solid fa-store"></i>Selo · acesso
                </span>
            </div>

            <h1 class="auth-title">Iniciar <span style="color: var(--accent-1);">Sessão</span></h1>
            <p class="auth-subtitle">Aceda à sua conta inserindo as suas credenciais abaixo.</p>

            <!-- Session Status Alert -->
            @if (session('status'))
                <div class="auth-alert alert-success mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <!-- Errors Alert -->
            @if ($errors->any())
                <div class="auth-alert alert-danger mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold" style="font-size: 0.95rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label-stamp">Endereço de Email</label>
                    <input type="email"
                           class="market-input @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="o-seu-email@exemplo.pt"
                           required
                           autofocus
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
                               placeholder="A sua palavra-passe"
                               required
                               autocomplete="current-password"
                               style="padding: 12px 16px; font-family: 'Inter', sans-serif; font-size: 0.98rem; color: #182b20;">
                        <button type="button" class="toggle-pw-btn" id="togglePassword" aria-label="Mostrar ou ocultar palavra-passe" style="background: transparent; border-left: 2px solid var(--ink); padding: 0 16px; color: var(--muted); cursor: pointer;">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @if (Route::has('password.request'))
                        <div class="mt-2 text-end auth-forgot">
                            <a href="{{ route('password.request') }}" class="small text-decoration-underline" style="color: #44594c;">
                                Esqueceu-se da sua palavra-passe?
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="mb-4 form-check d-flex align-items-center gap-2 ps-0">
                    <input type="checkbox"
                           class="form-check-input ms-0 me-2"
                           id="remember_me"
                           name="remember"
                           style="width: 1.15em; height: 1.15em; border: 2px solid var(--ink); border-radius: 4px; cursor: pointer;">
                    <label class="form-check-label small text-muted" for="remember_me" style="cursor: pointer;">
                        Lembrar-me neste dispositivo
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn-mkt-submit w-100 border-0">
                        Entrar na Plataforma
                    </button>
                </div>
            </form>

            <div class="auth-link-line pt-3 mt-3 border-top text-center" style="border-color: rgba(24, 43, 32, 0.12) !important; font-size: 1rem;">
                <span style="color: #44594c;">Ainda não tem conta?</span>
                <a href="{{ route('register') }}" class="auth-link ms-1 fw-bold" style="font-size: 1rem;">Criar Conta Gratuita</a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    if (isPassword) {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
    @endpush
</x-market-layout>
