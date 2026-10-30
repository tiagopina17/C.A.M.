<x-market-layout title="Página Não Encontrada (404) — Cá do Bairro">
    <div class="d-flex align-items-center justify-content-center py-5 min-vh-75">
        <div class="error-container">
            <span class="badge-stamp mb-3">
                <i class="fas fa-compass me-1"></i>Selo · erro 404
            </span>
            <div class="error-icon">
                <i class="fas fa-map-location-dot"></i>
            </div>
            <div class="error-code">404</div>
            <h1 class="error-title">Página <span style="color: var(--accent-1);">Não Encontrada</span></h1>
            <p class="error-message">
                A página que procura não existe, mudou de endereço ou foi removida.<br>
                Explore os estabelecimentos do bairro ou regresse à página inicial.
            </p>

            <div class="error-actions">
                <a href="{{ route('home') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold" style="border-radius: 12px; border-width: 2px;">
                    <i class="fas fa-house me-2"></i>Voltar ao Início
                </a>
                <a href="{{ route('explore') }}" class="btn text-white px-4 py-2 fw-semibold" style="background: var(--accent-1); border-radius: 12px;">
                    <i class="fas fa-store me-2"></i>Explorar Lojas
                </a>
                <a href="{{ route('map') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold" style="border-radius: 12px;">
                    <i class="fas fa-map me-2"></i>Ver no Mapa
                </a>
            </div>
        </div>
    </div>
</x-market-layout>

