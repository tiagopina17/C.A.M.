<x-market-layout title="Lojas — Cá do Bairro">
    <div class="explore-page-wrapper py-5">
        <main class="container">
            <!-- PAGE HEADER -->
            <div class="text-center mb-5">
                <span class="badge-stamp">
                    <i class="fas fa-store"></i>Comércio de Proximidade
                </span>
                <h1 class="bairro-hero-title fs-1 mt-2 mb-2">Lojas e Mercados do Bairro</h1>
                <p class="hero-subtitle mx-auto" style="max-width: 580px;">
                    Descubra as mercearias, padarias, talhos e lojas tradicionais da sua terra. Compre perto e apoie quem dá vida à sua rua.
                </p>

                <!-- SEARCH & FILTER BAR -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-6 col-md-8">
                        <div class="search-card" role="search" aria-label="Pesquisar lojas">
                            <input 
                                id="store-explore-search" 
                                name="q" 
                                class="search-input" 
                                type="search" 
                                placeholder="Procurar lojas pelo nome ou produto..." 
                                value="{{ $searchQuery ?? '' }}" 
                                autocomplete="off" 
                            />
                            <button id="search-submit-btn" class="search-icon-btn" type="button" aria-label="Pesquisar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CATEGORY FILTER CHIPS -->
                @if($categories->isNotEmpty())
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-4" id="category-filter-chips">
                        <a href="{{ route('explore') }}" 
                           data-filter-slug=""
                           class="category-filter-pill {{ empty($selectedCategory) ? 'active' : '' }}">
                            Todas as Lojas
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('explore', ['categoria' => $category->slug]) }}" 
                               data-filter-slug="{{ $category->slug }}"
                               class="category-filter-pill {{ $selectedCategory === $category->slug || $selectedCategory === $category->name ? 'active' : '' }}">
                                @if($category->icon)
                                    <i class="fas {{ $category->icon }} me-1"></i>
                                @endif
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- SEARCH / FILTER STATUS -->
            <div id="filter-status-bar" class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom" style="border-color: var(--line) !important;">
                <div class="small text-muted" id="filter-status-text">
                    A mostrar <strong id="stores-count-badge">{{ $stores->total() }}</strong> {{ $stores->total() === 1 ? 'loja' : 'lojas' }}
                </div>
                <a href="{{ route('explore') }}" id="clear-filters-btn" class="small text-decoration-none text-danger fw-semibold" style="{{ (empty($searchQuery) && empty($selectedCategory)) ? 'display: none;' : '' }}">
                    <i class="fas fa-times-circle me-1"></i>Limpar filtros
                </a>
            </div>

            <!-- STORES GRID -->
            @if($stores->isNotEmpty())
                <div class="row g-4 mb-5" id="stores-grid">
                    @foreach($stores as $store)
                        @php
                            $storeCategories = $store->services->pluck('category')->filter()->unique('id');
                            $categorySlugs = $storeCategories->pluck('slug')->implode(' ');
                            $categoryNames = $storeCategories->pluck('name')->implode(' ');
                        @endphp
                        <div class="col-lg-4 col-md-6 store-item-col" 
                             data-name="{{ mb_strtolower($store->name) }}"
                             data-description="{{ mb_strtolower($store->description ?? '') }}"
                             data-categories="{{ mb_strtolower($categorySlugs . ' ' . $categoryNames) }}">
                            <article class="store-card h-100 d-flex flex-column justify-content-between p-4">
                                <div>
                                    <!-- Store Header -->
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        @if(!empty($store->logo_path))
                                            <img src="{{ asset('storage/' . $store->logo_path) }}" 
                                                 alt="{{ $store->name }}" 
                                                 class="store-card-logo" />
                                        @else
                                            <div class="store-card-initial" aria-hidden="true">
                                                {{ mb_strtoupper(mb_substr($store->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex-grow-1 min-w-0">
                                            <h2 class="h5 fw-bold text-dark mb-1 text-truncate" title="{{ $store->name }}">
                                                {{ $store->name }}
                                            </h2>
                                            @if($storeCategories->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($storeCategories->take(2) as $cat)
                                                        <span class="store-tag">{{ $cat->name }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="store-tag text-muted">Comércio Local</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="small text-muted mb-3" style="line-height: 1.55; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $store->description ?: 'Visite esta loja de bairro para descobrir os seus produtos e novidades da semana.' }}
                                    </p>
                                </div>

                                <!-- Card Footer -->
                                <div class="pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: rgba(24, 43, 32, 0.08) !important;">
                                    <div class="small text-muted">
                                        <i class="fas fa-location-dot text-success me-1"></i>
                                        <span>No bairro</span>
                                    </div>
                                    <a href="{{ route('stores.show', $store) }}" class="btn-store-visit">
                                        Ver Loja <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <!-- INSTANT EMPTY STATE (HIDDEN BY DEFAULT) -->
                <div id="instant-empty-state" class="text-center py-5 my-4" style="display: none;">
                    <div class="store-empty-icon mb-3">
                        <i class="fas fa-store-slash fa-3x" style="color: var(--muted); opacity: 0.5;"></i>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-2">Nenhuma loja encontrada</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 440px;">
                        Não foram encontradas lojas que correspondam à sua pesquisa ou categoria selecionada.
                    </p>
                    <button type="button" id="reset-filters-btn" class="btn-pill-mustard border-0 px-4 py-2">
                        Ver todas as lojas
                    </button>
                </div>

                <!-- SERVER PAGINATION (if multiple pages exist) -->
                @if($stores->hasPages())
                    <div class="d-flex justify-content-center mt-4" id="server-pagination">
                        {{ $stores->links() }}
                    </div>
                @endif
            @else
                <!-- SERVER EMPTY STATE -->
                <div class="text-center py-5 my-4">
                    <div class="store-empty-icon mb-3">
                        <i class="fas fa-store-slash fa-3x" style="color: var(--muted); opacity: 0.5;"></i>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-2">Nenhuma loja encontrada</h3>
                    <p class="text-muted mx-auto mb-4" style="max-width: 440px;">
                        Ainda não existem lojas registadas na plataforma.
                    </p>
                    <a href="{{ route('explore') }}" class="btn-pill-mustard text-decoration-none px-4 py-2">
                        Ver todas as lojas
                    </a>
                </div>
            @endif
        </main>
    </div>

    @push('styles')
    <style>
        .store-item-col {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
        .category-filter-pill {
            cursor: pointer;
            user-select: none;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('store-explore-search');
        const clearBtn = document.getElementById('clear-filters-btn');
        const resetBtn = document.getElementById('reset-filters-btn');
        const countBadge = document.getElementById('stores-count-badge');
        const emptyState = document.getElementById('instant-empty-state');
        const storeCols = Array.from(document.querySelectorAll('.store-item-col'));
        const pills = Array.from(document.querySelectorAll('#category-filter-chips .category-filter-pill'));
        const pagination = document.getElementById('server-pagination');

        if (storeCols.length === 0) return;

        // Current active filters
        let activeCategory = '{{ $selectedCategory ?? '' }}'.toLowerCase();
        let searchQuery = (searchInput ? searchInput.value.trim().toLowerCase() : '');

        function applyFilters() {
            let visibleCount = 0;

            storeCols.forEach(function (col) {
                const name = col.getAttribute('data-name') || '';
                const desc = col.getAttribute('data-description') || '';
                const cats = col.getAttribute('data-categories') || '';

                // Category match: true if no category chosen or if cats contains activeCategory
                const matchCategory = !activeCategory || cats.includes(activeCategory);

                // Search match: true if no search term or if name/desc contains searchQuery
                const matchSearch = !searchQuery || name.includes(searchQuery) || desc.includes(searchQuery);

                if (matchCategory && matchSearch) {
                    col.style.display = '';
                    visibleCount++;
                } else {
                    col.style.display = 'none';
                }
            });

            // Update visible count
            if (countBadge) {
                countBadge.textContent = visibleCount;
            }

            // Toggle empty state
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
            }

            // Toggle clear button
            if (clearBtn) {
                clearBtn.style.display = (!activeCategory && !searchQuery) ? 'none' : 'inline-block';
            }

            // Hide server pagination when filtering client-side
            if (pagination) {
                pagination.style.display = (activeCategory || searchQuery) ? 'none' : '';
            }

            // Update URL query string without reloading page
            const params = new URLSearchParams();
            if (activeCategory) params.set('categoria', activeCategory);
            if (searchQuery) params.set('q', searchQuery);
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }

        // Category click handlers
        pills.forEach(function (pill) {
            pill.addEventListener('click', function (e) {
                e.preventDefault();

                pills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');

                activeCategory = (this.getAttribute('data-filter-slug') || '').toLowerCase();
                applyFilters();
            });
        });

        // Instant search as you type
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                searchQuery = this.value.trim().toLowerCase();
                applyFilters();
            });
        }

        // Reset & Clear handlers
        function resetAll() {
            activeCategory = '';
            searchQuery = '';
            if (searchInput) searchInput.value = '';

            pills.forEach(function (p) {
                if (p.getAttribute('data-filter-slug') === '') {
                    p.classList.add('active');
                } else {
                    p.classList.remove('active');
                }
            });

            applyFilters();
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetAll();
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();
                resetAll();
            });
        }
    });
    </script>
    @endpush
</x-market-layout>
