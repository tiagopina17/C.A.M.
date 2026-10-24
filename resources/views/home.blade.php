<x-market-layout title="Cá do Bairro — Apoio ao Comércio Local">
    <div class="hero-page-wrapper">
        <main class="container mt-4">
            <!-- HERO -->
            <section class="hero-grid">
                <div>
                    <span class="badge-stamp">
                        <i class="fas fa-store"></i>Selo · comércio local
                    </span>
                    <h1 class="bairro-hero-title">
                        Tudo o que precisa,<br class="d-none d-md-block">
                        <span class="hero-bairro-ca">Cá do</span> <span class="hero-bairro-name">Bairro.</span>
                    </h1>
                    <p class="hero-subtitle">Descubra mercearias, mercados e lojas tradicionais à sua porta.</p>

                    <!-- Search Box -->
                    <div class="search-container position-relative">
                        <div class="search-card" role="search" aria-label="Pesquisar lojas">
                            <input id="store-search" class="search-input" type="search" placeholder="Procure por lojas..." aria-autocomplete="list" aria-controls="search-suggestions" autocomplete="off" />
                            <button id="search-btn" class="search-icon-btn" type="button" aria-label="Pesquisar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div id="search-suggestions" class="search-suggestions" role="listbox" aria-label="Resultados da pesquisa"></div>
                    </div>
                </div>

                <!-- Ticket Frame Illustration -->
                <div class="hero-visual">
                    <div class="ticket-frame">
                        <div class="ticket-stamp">100%<br>bairro</div>
                        <svg class="hero-abstract" viewBox="0 0 500 420" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustração Cá do Bairro">
                            <!-- Background shapes -->
                            <circle cx="150" cy="150" r="140" fill="var(--paper-2)" />
                            <circle cx="360" cy="270" r="100" fill="var(--accent-1)" opacity="0.10" />
                            <rect x="60" y="260" width="140" height="140" rx="28" fill="var(--ochre)" opacity="0.14" transform="rotate(8 130 330)" />

                            <!-- Dashed accent circles -->
                            <circle cx="410" cy="90" r="34" fill="none" stroke="var(--ochre)" stroke-width="3" stroke-dasharray="6 7" />
                            <circle cx="80" cy="330" r="22" fill="none" stroke="var(--accent-2)" stroke-width="3" stroke-dasharray="5 6" />

                            <!-- Location pin -->
                            <g transform="translate(300,120)">
                                <path d="M0 0 C 46 0 46 46 0 100 C -46 46 -46 0 0 0 Z" fill="var(--accent-1)" />
                                <circle cx="0" cy="34" r="16" fill="var(--paper)" />
                            </g>

                            <!-- Storefront icon -->
                            <g transform="translate(140,190)">
                                <rect x="-38" y="6" width="76" height="60" rx="6" fill="var(--card)" stroke="var(--ink)" stroke-width="3" />
                                <path d="M-46 6 L-30 -26 L30 -26 L46 6 Z" fill="var(--ochre)" stroke="var(--ink)" stroke-width="3" />
                                <rect x="-10" y="34" width="20" height="32" fill="var(--accent-2)" />
                            </g>

                            <!-- Magnifying glass -->
                            <g transform="translate(220,300)">
                                <circle cx="0" cy="0" r="46" fill="var(--card)" stroke="var(--ink)" stroke-width="6" />
                                <line x1="33" y1="33" x2="70" y2="70" stroke="var(--ink)" stroke-width="8" stroke-linecap="round" />
                                <circle cx="0" cy="0" r="46" fill="none" stroke="var(--accent-1)" stroke-width="3" opacity="0.5" />
                            </g>

                            <!-- Confetti dots -->
                            <circle cx="430" cy="200" r="6" fill="var(--accent-2)" />
                            <circle cx="60" cy="90" r="5" fill="var(--ochre)" />
                            <circle cx="380" cy="360" r="7" fill="var(--accent-1)" />
                        </svg>
                    </div>
                </div>
            </section>

            <!-- FAQ ACCORDION SECTION (MARKET AWNING) -->
            <div class="scallop mt-5"></div>
            <section class="features-accordion-section">
                <div class="features-inner">
                    <div class="text-center mb-4">
                        <span class="badge-chip">
                            <i class="fa-solid fa-circle-question"></i>Perguntas Frequentes
                        </span>
                        <h2 class="bairro-hero-title fs-2 mt-2 mb-0 text-white">Dúvidas sobre o Cá do Bairro?</h2>
                    </div>

                    <div class="accordion" id="featuresAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#featureOne" aria-expanded="true" aria-controls="featureOne">
                                    <span class="faq-num">01</span>
                                    <span>O que é o Cá do Bairro?</span>
                                </button>
                            </h2>
                            <div id="featureOne" class="accordion-collapse collapse show" data-bs-parent="#featuresAccordion">
                                <div class="accordion-body">
                                    É uma montra digital que reúne as mercearias, mercados e lojas tradicionais da sua localidade. O objetivo é ajudar os vizinhos a encontrar comércio de proximidade e fortalecer a economia local.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureTwo" aria-expanded="false" aria-controls="featureTwo">
                                    <span class="faq-num">02</span>
                                    <span>A quem se destina?</span>
                                </button>
                            </h2>
                            <div id="featureTwo" class="accordion-collapse collapse" data-bs-parent="#featuresAccordion">
                                <div class="accordion-body">
                                    A todas as pessoas que pretendem comprar localmente a quem conhecem, e a comerciantes e merceeiros que queiram dar visibilidade à sua loja sem complicações técnicas.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#featureThree" aria-expanded="false" aria-controls="featureThree">
                                    <span class="faq-num">03</span>
                                    <span>Como funciona?</span>
                                </button>
                            </h2>
                            <div id="featureThree" class="accordion-collapse collapse" data-bs-parent="#featuresAccordion">
                                <div class="accordion-body">
                                    Pode pesquisar pelo nome da loja, filtrar por localidade e categorias na secção Lojas, ou consultar o mapa interativo para ver exatamente o que existe perto de si.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="scallop scallop-flip mb-5"></div>

            <!-- MERCHANT CALL TO ACTION -->
            <section class="merchant-cta-card my-5 p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="mb-2">
                            <span class="badge-stamp">
                                <i class="fas fa-store"></i>Comerciantes & Lojas
                            </span>
                        </div>
                        <h2 class="bairro-cta-title mb-3">Tem uma loja ou mercearia no bairro?</h2>
                        <p class="bairro-cta-text mb-4">
                            Junte-se à rede Cá do Bairro. Crie a montra digital da sua loja, destaque os seus produtos, e receba novos clientes da sua comunidade de forma simples e gratuita.
                        </p>
                        <div class="row g-2 text-dark small fw-medium">
                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Presença no mapa da cidade</span>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Catálogo de produtos da loja</span>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Gestão de equipa com códigos de convite</span>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success"></i>
                                <span>Painel de administração simples</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end text-center">
                        <div class="d-flex flex-column gap-3">
                            @guest
                                <a href="{{ route('register') }}" class="btn-main-cta fs-6 text-center">
                                    <i class="fas fa-user-plus me-2"></i>Registar a Minha Loja
                                </a>
                                <a href="{{ url('/ajuda') }}" class="btn-ghost text-dark text-center" style="border-color: var(--ink);">
                                    <i class="fas fa-circle-question me-2"></i>Como Funciona
                                </a>
                            @else
                                <a href="{{ url('/gestao/lojas') }}" class="btn-main-cta fs-6 text-center">
                                    <i class="fas fa-store me-2"></i>Área de Gestão
                                </a>
                                <a href="{{ url('/mapa') }}" class="btn-ghost text-dark text-center" style="border-color: var(--ink);">
                                    <i class="fas fa-map-location-dot me-2"></i>Ver Lojas no Mapa
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Preloaded stores from database
            const stores = @json($stores ?? []);
            const searchInput = document.getElementById('store-search');
            const suggestionsDiv = document.getElementById('search-suggestions');

            if (!searchInput || !suggestionsDiv) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();

                if (query.length === 0) {
                    suggestionsDiv.innerHTML = '';
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                // Filter stores that match query
                const matches = stores.filter(store => {
                    const storeName = (store.name || '').toLowerCase();
                    return storeName.includes(query);
                });

                if (matches.length > 0) {
                    suggestionsDiv.innerHTML = matches.map(store => `
                        <div class="suggestion-item" data-id="${store.id}">
                            <i class="fas fa-store me-2 text-success"></i>
                            <span>${escapeHtml(store.name)}</span>
                        </div>
                    `).join('');
                    suggestionsDiv.style.display = 'block';

                    // Add click handlers
                    suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                        item.addEventListener('click', function () {
                            const storeId = this.getAttribute('data-id');
                            window.location.href = `{{ url('/lojas') }}/${storeId}`;
                        });
                    });
                } else {
                    suggestionsDiv.innerHTML = '<div class="suggestion-item no-results">Nenhuma loja encontrada</div>';
                    suggestionsDiv.style.display = 'block';
                }
            });

            // Close on click outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.style.display = 'none';
                }
            });

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
    @endpush
</x-market-layout>
