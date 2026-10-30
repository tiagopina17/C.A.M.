<x-market-layout title="Erro Interno (500) — Cá do Bairro">
    <div class="d-flex align-items-center justify-content-center py-5 min-vh-75">
        <div class="error-container">
            <span class="badge-stamp mb-3">
                <i class="fas fa-triangle-exclamation me-1"></i>Selo · erro 500
            </span>
            <div class="error-icon">
                <i class="fas fa-wrench"></i>
            </div>
            <div class="error-code">500</div>
            <h1 class="error-title">Erro <span style="color: var(--accent-1);">de Servidor</span></h1>
            <p class="error-message">
                Ocorreu um problema inesperado no servidor ao processar o seu pedido.<br>
                Estamos a trabalhar para resolver. Por favor tente novamente daqui a instantes.
            </p>

            <div class="error-actions">
                <a href="{{ route('home') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold" style="border-radius: 12px; border-width: 2px;">
                    <i class="fas fa-house me-2"></i>Voltar ao Início
                </a>
                <a href="{{ route('help') }}" class="btn text-white px-4 py-2 fw-semibold" style="background: var(--accent-1); border-radius: 12px;">
                    <i class="fas fa-circle-question me-2"></i>Centro de Ajuda
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold" style="border-radius: 12px;">
                    <i class="fas fa-envelope me-2"></i>Fale Connosco
                </a>
            </div>
        </div>
    </div>
</x-market-layout>

