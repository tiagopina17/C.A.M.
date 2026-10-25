<x-market-layout :title="$store->name . ' — Cá do Bairro'">
    @push('styles')
        <style>
            .store-hero-card {
                background: var(--card, #fffdf7);
                border: 1px solid rgba(24, 43, 32, 0.12);
                border-radius: 18px;
                padding: 2.25rem;
                box-shadow: 0 4px 18px rgba(24, 43, 32, 0.05);
                position: relative;
                overflow: hidden;
            }
            .store-hero-card::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 6px;
                background: repeating-linear-gradient(90deg, var(--accent-1, #0f7a42) 0, var(--accent-1, #0f7a42) 18px, var(--ochre, #c17f1e) 18px, var(--ochre, #c17f1e) 36px);
            }
            .store-detail-avatar {
                width: 104px;
                height: 104px;
                border-radius: 50%;
                background: var(--ink, #182b20);
                color: var(--card, #fffdf7);
                font-family: var(--font-display, 'Bricolage Grotesque', sans-serif);
                font-size: 2.25rem;
                font-weight: 800;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 4px solid var(--ochre, #c17f1e);
                box-shadow: 0 4px 12px rgba(24, 43, 32, 0.12);
                flex-shrink: 0;
            }
            .store-detail-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
            }
            .service-pill-card {
                background: #ffffff;
                border: 1px solid rgba(24, 43, 32, 0.09);
                border-radius: 12px;
                padding: 1rem 1.15rem;
                transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            }
            .service-pill-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 14px rgba(24, 43, 32, 0.06);
                border-color: rgba(15, 122, 66, 0.3);
            }
            .service-pill-icon {
                width: 44px;
                height: 44px;
                border-radius: 10px;
                background: rgba(15, 122, 66, 0.1);
                color: var(--accent-1, #0f7a42);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }
            .side-info-card {
                background: var(--card, #fffdf7);
                border: 1px solid rgba(24, 43, 32, 0.12);
                border-radius: 16px;
                padding: 1.5rem;
                box-shadow: 0 4px 14px rgba(24, 43, 32, 0.04);
            }
            .store-mini-map-box {
                height: 260px;
                min-height: 260px;
                border-radius: 12px;
                overflow: hidden;
                border: 1px solid rgba(24, 43, 32, 0.12);
                background: #e9ecef;
                position: relative;
            }
            .btn-outline-market {
                border: 1.5px solid rgba(24, 43, 32, 0.2);
                color: var(--ink, #182b20);
                background: #ffffff;
                border-radius: 9999px;
                font-weight: 600;
                padding: 0.5rem 1.15rem;
                font-size: 0.9rem;
                text-decoration: none;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
            }
            .btn-outline-market:hover {
                background: rgba(24, 43, 32, 0.06);
                color: var(--ink, #182b20);
                border-color: var(--ink, #182b20);
            }
            .share-toast {
                position: fixed;
                bottom: 24px;
                right: 24px;
                background: var(--ink, #182b20);
                color: #ffffff;
                padding: 12px 20px;
                border-radius: 10px;
                font-size: 0.9rem;
                box-shadow: 0 8px 24px rgba(0,0,0,0.2);
                display: none;
                z-index: 9999;
                align-items: center;
                gap: 8px;
            }
        </style>
    @endpush

    <div class="py-4 py-md-5" style="background-color: var(--paper, #f5f2e6); min-height: 85vh;">
        <div class="container">
            <!-- TOP BREADCRUMB & BACK BUTTON -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Início</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('explore') }}" class="text-decoration-none text-muted">Lojas</a></li>
                        <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $store->name }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('explore') }}" class="btn-outline-market">
                        <i class="fas fa-arrow-left"></i>
                        <span>Voltar às Lojas</span>
                    </a>
                    <button type="button" class="btn-outline-market" onclick="shareStore('{{ addslashes($store->name) }}', '{{ url()->current() }}')">
                        <i class="fas fa-share-nodes"></i>
                        <span>Partilhar</span>
                    </button>
                </div>
            </div>

            <!-- STORE HERO HEADER -->
            <div class="store-hero-card mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-md-auto text-center text-md-start">
                        <div class="store-detail-avatar mx-auto mx-md-0" aria-label="Logótipo de {{ $store->name }}">
                            @if(!empty($store->logo_path))
                                <img src="{{ asset('storage/' . $store->logo_path) }}" alt="{{ $store->name }}">
                            @else
                                @php
                                    // Strip mock tags, brackets, and Portuguese stopwords to get clean initials
                                    $cleanName = trim(preg_replace('/^\[.*?\]\s*/', '', $store->name));
                                    $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $cleanName);
                                    $stopwords = ['de', 'da', 'do', 'dos', 'das', 'e', 'a', 'o', '&'];
                                    $words = array_values(array_filter(
                                        preg_split('/\s+/', trim($cleanName)),
                                        fn ($w) => !in_array(mb_strtolower($w), $stopwords) && !empty($w)
                                    ));
                                    $initials = '';
                                    if (!empty($words)) {
                                        $initials .= mb_substr($words[0], 0, 1);
                                        if (isset($words[1])) {
                                            $initials .= mb_substr($words[1], 0, 1);
                                        }
                                    }
                                @endphp
                                <span>{{ mb_strtoupper($initials ?: 'CB') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                            <h1 class="h2 fw-bold text-dark mb-0" style="font-family: var(--font-display, 'Bricolage Grotesque', sans-serif); letter-spacing: -0.02em;">
                                {{ $store->name }}
                            </h1>
                        </div>

                        <!-- Categories -->
                        @if($categories->isNotEmpty())
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach($categories as $cat)
                                    <span class="store-tag">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Description -->
                        <p class="text-secondary mb-3" style="line-height: 1.65; font-size: 1rem;">
                            {{ $store->description ?: 'Uma loja de bairro tradicional com atendimento próximo e produtos selecionados. Visite-nos e sinta-se em casa.' }}
                        </p>

                        <!-- Quick Badges -->
                        @if($store->users->count() > 0)
                            <div class="small text-muted">
                                <i class="fas fa-users text-success me-1"></i>
                                <span>{{ $store->users->count() }} {{ $store->users->count() === 1 ? 'membro na equipa' : 'membros na equipa' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- USEFUL INFO STRIP -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="side-info-card py-3 px-3 d-flex align-items-center gap-3 h-100">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(15, 122, 66, 0.1); color: var(--accent-1, #0f7a42); font-size: 1.15rem;">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.04em;">Membro da Rede</div>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">No bairro desde {{ $store->created_at ? $store->created_at->format('d/m/Y') : 'início' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="side-info-card py-3 px-3 d-flex align-items-center gap-3 h-100">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(193, 127, 30, 0.1); color: var(--ochre, #c17f1e); font-size: 1.15rem;">
                            <i class="far fa-clock"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.04em;">Horário de Atendimento</div>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">Horário habitual de bairro</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="side-info-card py-3 px-3 d-flex align-items-center gap-3 h-100">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; background: rgba(24, 43, 32, 0.08); color: var(--ink, #182b20); font-size: 1.15rem;">
                            <i class="fa-solid fa-basket-shopping"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.04em;">Catálogo no Bairro</div>
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $store->services->count() }} {{ $store->services->count() === 1 ? 'artigo registado' : 'artigos registados' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT GRID -->
            <div class="row g-4">
                <!-- LEFT COLUMN: PRODUCTS & SERVICES (8 cols) -->
                <div class="col-lg-8">
                    <div class="side-info-card mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                            <div>
                                <h2 class="h5 fw-bold text-dark mb-1" style="font-family: var(--font-display, 'Bricolage Grotesque', sans-serif);">
                                    <i class="fas fa-boxes-stacked me-2" style="color: var(--accent-1, #0f7a42);"></i>Produtos e Especialidades
                                </h2>
                                <p class="small text-muted mb-0">O que encontra com facilidade nesta loja tradicional</p>
                            </div>
                            <span class="badge" style="background-color: rgba(193, 127, 30, 0.12); color: var(--ochre, #c17f1e); font-weight: 700; padding: 0.45rem 0.8rem; border-radius: 9999px;">
                                {{ $store->services->count() }} {{ $store->services->count() === 1 ? 'artigo registado' : 'artigos registados' }}
                            </span>
                        </div>

                        @if($store->services->isNotEmpty())
                            @foreach($servicesByCategory as $categoryName => $services)
                                <div class="mb-4">
                                    <h3 class="h6 fw-bold text-muted text-uppercase tracking-wider mb-3 d-flex align-items-center gap-2" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                                        <i class="fas fa-tag small text-success"></i>
                                        <span>{{ $categoryName }}</span>
                                        <span class="text-secondary fw-normal">({{ $services->count() }})</span>
                                    </h3>
                                    <div class="row g-3">
                                        @foreach($services as $service)
                                            @php
                                                $sName = mb_strtolower($service->name);
                                                $cName = mb_strtolower($categoryName);
                                                $icon = 'fa-basket-shopping';
                                                if (str_contains($sName, 'pão') || str_contains($sName, 'bolo') || str_contains($sName, 'padaria') || str_contains($cName, 'padaria')) {
                                                    $icon = 'fa-bread-slice';
                                                } elseif (str_contains($sName, 'carne') || str_contains($sName, 'talho') || str_contains($cName, 'talho')) {
                                                    $icon = 'fa-drumstick-bite';
                                                } elseif (str_contains($sName, 'fruta') || str_contains($sName, 'maçã') || str_contains($sName, 'laranja') || str_contains($sName, 'legume') || str_contains($sName, 'alface')) {
                                                    $icon = 'fa-leaf';
                                                } elseif (str_contains($sName, 'peixe') || str_contains($cName, 'peixaria')) {
                                                    $icon = 'fa-fish';
                                                } elseif (str_contains($sName, 'queijo') || str_contains($sName, 'leite') || str_contains($cName, 'laticínios')) {
                                                    $icon = 'fa-cheese';
                                                } elseif (str_contains($sName, 'café') || str_contains($sName, 'chá') || str_contains($sName, 'bebida')) {
                                                    $icon = 'fa-mug-hot';
                                                } elseif (str_contains($sName, 'pasta') || str_contains($sName, 'sabonete') || str_contains($cName, 'higiene')) {
                                                    $icon = 'fa-soap';
                                                }
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="service-pill-card d-flex align-items-center gap-3">
                                                    <div class="service-pill-icon">
                                                        <i class="fa-solid {{ $icon }}"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="fw-bold text-dark text-truncate" title="{{ $service->name }}">
                                                            {{ $service->name }}
                                                        </div>
                                                        <div class="small text-muted">{{ $categoryName }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 px-3" style="background: rgba(24, 43, 32, 0.02); border-radius: 12px; border: 1px dashed rgba(24, 43, 32, 0.15);">
                                <i class="fas fa-store-slash fa-2x mb-2 text-muted" style="opacity: 0.4;"></i>
                                <h3 class="h6 fw-bold text-dark mb-1">Catálogo em atualização</h3>
                                <p class="small text-muted mb-0" style="max-width: 420px; margin: 0 auto;">
                                    Esta mercearia ainda está a abastecer as suas prateleiras digitais. Visite o espaço físico no bairro para conhecer todas as novidades!
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- COMMUNITY & REVIEWS NOTICE -->
                    <div class="side-info-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(193, 127, 30, 0.1); color: var(--ochre, #c17f1e); font-size: 1.25rem;">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div>
                                <h3 class="h6 fw-bold text-dark mb-1">Apoie o comércio de proximidade</h3>
                                <p class="small text-muted mb-0">
                                    Comprar no bairro valoriza a economia local, reduz distâncias e constrói laços reais entre vizinhos. Recomende a <strong>{{ $store->name }}</strong> a quem mora perto!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: LOCATION & STATS (4 cols) -->
                <div class="col-lg-4">
                    <!-- LOCATION MINI-MAP -->
                    <div class="side-info-card mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h6 fw-bold text-dark mb-0" style="font-family: var(--font-display, 'Bricolage Grotesque', sans-serif);">
                                <i class="fas fa-map-location-dot me-2" style="color: var(--accent-1, #0f7a42);"></i>Localização
                            </h2>
                            @if($store->lat && $store->lon)
                                <a href="https://www.google.com/maps?q={{ $store->lat }},{{ $store->lon }}" target="_blank" rel="noopener noreferrer" class="small fw-semibold text-decoration-none" style="color: var(--ochre, #c17f1e);">
                                    Abrir Mapa <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            @endif
                        </div>

                        @if($store->lat && $store->lon)
                            <div class="store-mini-map-box mb-3 position-relative" id="store-mini-map" style="height: 260px; min-height: 260px;">
                                <div id="mini-map-loading" class="d-flex align-items-center justify-content-center h-100 text-muted small position-absolute top-0 start-0 w-100" style="background: #e9ecef; z-index: 2;">
                                    <div class="spinner-border spinner-border-sm me-2 text-success" role="status"></div>
                                    <span>A carregar mapa...</span>
                                </div>
                            </div>
                            <div class="d-grid">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $store->lat }},{{ $store->lon }}" target="_blank" rel="noopener noreferrer" class="btn-pill-mustard text-center text-decoration-none py-2 fw-semibold">
                                    <i class="fas fa-diamond-turn-right me-1"></i>Como Chegar
                                </a>
                            </div>
                        @else
                            <div class="p-4 text-center rounded-3" style="background: rgba(24, 43, 32, 0.03); border: 1px dashed rgba(24, 43, 32, 0.15);">
                                <i class="fas fa-location-crosshairs fa-2x text-muted mb-2" style="opacity: 0.4;"></i>
                                <p class="small text-muted mb-0">Localização exata por GPS ainda não configurada para esta loja.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SHARE TOAST NOTIFICATION -->
    <div id="share-toast" class="share-toast" role="alert" aria-live="polite" style="display: none;">
        <i class="fas fa-check-circle text-success"></i>
        <span>Link da loja copiado para a área de transferência!</span>
    </div>

    @push('scripts')
        <script>
            function shareStore(storeName, url) {
                if (navigator.share) {
                    navigator.share({
                        title: `${storeName} - Cá do Bairro`,
                        text: `Conheça a loja ${storeName} no Cá do Bairro:`,
                        url: url
                    }).catch(() => {});
                } else if (navigator.clipboard) {
                    navigator.clipboard.writeText(url).then(() => {
                        showShareToast();
                    }).catch(() => {
                        prompt('Copie o link desta loja:', url);
                    });
                } else {
                    prompt('Copie o link desta loja:', url);
                }
            }

            function showShareToast() {
                const toast = document.getElementById('share-toast');
                if (toast) {
                    toast.style.display = 'flex';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 3500);
                }
            }
        </script>

        @if($store->lat && $store->lon && !empty($apiKey))
            <script>
                const STORE_LAT = {{ (float) $store->lat }};
                const STORE_LON = {{ (float) $store->lon }};
                const STORE_NAME = @json($store->name);
                const MAP_ID = "{{ $mapId }}";
                const MAPS_API_KEY = "{{ $apiKey }}";

                async function initStoreMap() {
                    const mapElement = document.getElementById('store-mini-map');
                    const loader = document.getElementById('mini-map-loading');
                    if (!mapElement) return;

                    try {
                        const { Map } = await google.maps.importLibrary("maps");
                        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                        const storePos = { lat: STORE_LAT, lng: STORE_LON };

                        const map = new Map(mapElement, {
                            center: storePos,
                            zoom: 16,
                            mapId: MAP_ID,
                            disableDefaultUI: true,
                            zoomControl: true,
                            streetViewControl: false,
                            mapTypeControl: false,
                        });

                        const pinWrapper = document.createElement('div');
                        pinWrapper.style.backgroundColor = '#0f7a42';
                        pinWrapper.style.color = '#ffffff';
                        pinWrapper.style.width = '38px';
                        pinWrapper.style.height = '38px';
                        pinWrapper.style.borderRadius = '50%';
                        pinWrapper.style.display = 'flex';
                        pinWrapper.style.alignItems = 'center';
                        pinWrapper.style.justifyContent = 'center';
                        pinWrapper.style.boxShadow = '0 3px 10px rgba(0,0,0,0.3)';
                        pinWrapper.style.border = '2.5px solid #ffffff';
                        pinWrapper.innerHTML = '<i class="fa-solid fa-store" style="font-size: 16px;"></i>';

                        new AdvancedMarkerElement({
                            map: map,
                            position: storePos,
                            title: STORE_NAME,
                            content: pinWrapper,
                        });

                        if (loader) {
                            loader.style.display = 'none';
                        }
                    } catch (err) {
                        console.error("Erro ao carregar mini-mapa:", err);
                        if (loader) {
                            loader.innerHTML = '<span class="text-muted small"><i class="fas fa-location-dot me-1 text-success"></i>{{ $store->lat }}, {{ $store->lon }}</span>';
                        }
                    }
                }

                // Google Maps API bootstrap loader
                (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
                    key: MAPS_API_KEY,
                    v: "weekly"
                });

                window.addEventListener('load', () => {
                    initStoreMap();
                });
            </script>
        @endif
    @endpush
</x-market-layout>
