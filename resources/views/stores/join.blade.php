<x-market-layout title="Juntar-se a uma Loja — Cá do Bairro">
    <div class="auth-page-wrapper py-5">
        <div class="auth-card position-relative shadow mx-auto" style="max-width: 540px;">
            <!-- Top striped banner -->
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px); border-top-left-radius: 18px; border-top-right-radius: 18px;"></div>

            <div class="text-center pt-2">
                <span class="badge-stamp">
                    <i class="fa-solid fa-door-open me-1"></i>Convite de Colaborador
                </span>
            </div>

            <h1 class="auth-title">Juntar-se a uma <span style="color: var(--accent-1);">Loja</span></h1>
            <p class="auth-subtitle">Introduza o código de convite de 7 carateres para associar a sua conta a um estabelecimento comercial.</p>

            <!-- Alerts -->
            @if (session('error'))
                <div class="auth-alert alert-danger mb-4 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold" style="font-size: 0.95rem;">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert alert-danger mb-4 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold" style="font-size: 0.95rem;">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <form action="{{ route('stores.join.submit') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="code" class="form-label-stamp text-center d-block">
                        Código de Convite (7 Caracteres) <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="market-input text-center text-uppercase fw-bold font-monospace fs-2 py-3 @error('code') is-invalid @enderror"
                           style="letter-spacing: 0.2em;"
                           id="code"
                           name="code"
                           value="{{ old('code', $code ?? '') }}"
                           maxlength="7"
                           placeholder="EX: A1B2C3D"
                           required
                           autofocus
                           autocomplete="off">
                    <small class="text-muted d-block text-center mt-2">
                        <i class="fa-solid fa-circle-info me-1 text-mustard"></i>O código deve ser fornecido pelo proprietário da loja.
                    </small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn-mkt-submit w-100 border-0 py-3">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Associar à Loja
                    </button>
                    <a href="{{ route('management.stores.index') }}" class="btn-mkt-ghost text-decoration-none text-center py-2">
                        Cancelar e Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-market-layout>

