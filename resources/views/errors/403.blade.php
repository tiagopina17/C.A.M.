<x-market-layout title="Acesso Negado (403) — Cá do Bairro">
    <div class="d-flex align-items-center justify-content-center py-5 min-vh-75">
        <div class="error-container">
            <span class="badge-stamp mb-3">
                <i class="fas fa-lock me-1"></i>Selo · erro 403
            </span>
            <div class="error-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div class="error-code">403</div>
            <h1 class="error-title">Acesso <span style="color: var(--accent-1);">Negado</span></h1>
            <p class="error-message">
                {{ $exception->getMessage() ?: 'Desculpe, você não tem permissão para aceder a esta página ou recurso.' }}<br>
                Se acredita que isto é um erro, por favor entre em <a href="{{ route('contact') }}">contacto connosco</a>.
            </p>

            <div class="error-actions">
                <a href="{{ route('home') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold" style="border-radius: 12px; border-width: 2px;">
                    <i class="fas fa-house me-2"></i>Voltar ao Início
                </a>
                @guest
                    <a href="{{ route('login') }}" class="btn text-white px-4 py-2 fw-semibold" style="background: var(--accent-1); border-radius: 12px;">
                        <i class="fas fa-arrow-right-to-bracket me-2"></i>Iniciar Sessão
                    </a>
                @endguest
                <a href="{{ route('help') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold" style="border-radius: 12px;">
                    <i class="fas fa-circle-question me-2"></i>Ajuda
                </a>
            </div>
        </div>
    </div>
</x-market-layout>

