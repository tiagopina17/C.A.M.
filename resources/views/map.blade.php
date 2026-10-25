<x-market-layout title="Mapa do Bairro — Cá do Bairro">
    <div class="map-page-wrapper py-5">
        <main class="container">
            <!-- PAGE HEADER -->
            <div class="text-center mb-4">
                <span class="badge-stamp">
                    <i class="fas fa-location-dot"></i>Mapa Interativo
                </span>
                <h1 class="bairro-hero-title fs-1 mt-2 mb-2">Mapa de Lojas do Bairro</h1>
                <p class="hero-subtitle mx-auto mb-3" style="max-width: 580px;">
                    Encontre no mapa as mercearias, padarias, talhos e lojas tradicionais mais próximas de si.
                </p>

                <div class="d-flex align-items-center justify-content-center gap-3">
                    <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill" id="map-stores-badge">
                        <i class="fas fa-store me-1 text-warning"></i>
                        {{ count($stores) }} {{ count($stores) === 1 ? 'loja no mapa' : 'lojas no mapa' }}
                    </span>
                    <a href="{{ route('explore') }}" class="btn-pill-mustard text-decoration-none px-3 py-1 fs-6">
                        <i class="fas fa-list me-1"></i>Ver lista de lojas
                    </a>
                </div>
            </div>

            <!-- MAP CONTAINER CARD -->
            <div class="map-card-wrapper position-relative mb-5">
                <div id="map" class="map-container" style="height: 600px; width: 100%; background-color: var(--paper-2, #ede6d2);"></div>

                <!-- Fallback Loading Indicator -->
                <div id="map-loading" class="position-absolute top-50 start-50 translate-middle text-center p-4 bg-white rounded-3 shadow border" style="z-index: 5;">
                    <div class="spinner-border text-success mb-2" role="status">
                        <span class="visually-hidden">A carregar mapa...</span>
                    </div>
                    <div class="fw-semibold text-dark">A carregar o mapa do bairro...</div>
                </div>

                @if(empty($apiKey))
                    <div class="position-absolute top-0 start-0 w-100 p-3 bg-warning text-dark text-center fw-semibold border-bottom" style="z-index: 10;">
                        <i class="fas fa-triangle-exclamation me-2"></i>
                        Chave Google Maps API não configurada em <code>.env</code> (defina <code>GOOGLE_MAPS_API_KEY</code>).
                    </div>
                @endif
            </div>
        </main>
    </div>

    @push('styles')
    <style>
        .map-card-wrapper {
            border: 2px solid var(--ink);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 6px 6px 0 rgba(24, 43, 32, 0.15);
            background: var(--card, #fffdf7);
        }

        .custom-map-pin {
            background-color: var(--ochre, #c17f1e);
            color: #fff;
            border: 2px solid var(--ink, #182b20);
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            box-shadow: 2px 2px 0 var(--ink, #182b20);
            cursor: pointer;
            transition: transform 0.15s ease, background-color 0.15s ease;
        }

        .custom-map-pin:hover {
            transform: scale(1.15);
            background-color: var(--accent-1, #0f7a42);
        }

        .map-infowindow {
            font-family: 'Inter', sans-serif;
            max-width: 260px;
            padding: 4px;
        }

        .map-infowindow h3 {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--ink, #182b20);
            margin: 0 0 6px 0;
        }

        .map-infowindow p {
            font-size: 0.85rem;
            color: #5c6b5f;
            margin: 0 0 10px 0;
            line-height: 1.45;
        }

        .map-infowindow .tag-badge {
            font-family: 'Space Mono', monospace;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(15, 122, 66, 0.1);
            color: #0f7a42;
            display: inline-block;
            margin-bottom: 8px;
        }

        .map-infowindow .btn-visit-pin {
            font-family: 'Bricolage Grotesque', sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
            background-color: #D9A441;
            color: #182b20;
            padding: 5px 12px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.15s;
        }

        .map-infowindow .btn-visit-pin:hover {
            background-color: #c1902f;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- MarkerClusterer library -->
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

    <script>
        const MAPS_API_KEY = "{{ $apiKey }}";
        const MAP_ID = "{{ $mapId }}";
        const STORES_DATA = @json($stores);

        let googleMap;
        let mapMarkers = [];
        let activeInfoWindow = null;

        async function initGoogleMap() {
            const loadingIndicator = document.getElementById('map-loading');

            if (!MAPS_API_KEY) {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                return;
            }

            try {
                const { Map } = await google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                // Default center (Lisbon, Portugal)
                const defaultCenter = { lat: 38.7169, lng: -9.1399 };

                googleMap = new Map(document.getElementById('map'), {
                    center: defaultCenter,
                    zoom: 13,
                    mapId: MAP_ID,
                });

                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }

                if (STORES_DATA.length === 0) return;

                const bounds = new google.maps.LatLngBounds();

                STORES_DATA.forEach((store, index) => {
                    const lat = parseFloat(store.lat);
                    const lng = parseFloat(store.lon);

                    if (isNaN(lat) || isNaN(lng)) return;

                    bounds.extend({ lat, lng });

                    // Custom marker DOM element with store icon
                    const pinElement = document.createElement('div');
                    pinElement.className = 'custom-map-pin';
                    pinElement.innerHTML = '<i class="fa-solid fa-store"></i>';
                    pinElement.setAttribute('title', store.name);

                    const marker = new AdvancedMarkerElement({
                        map: googleMap,
                        position: { lat, lng },
                        title: store.name,
                        content: pinElement,
                    });

                    // Build InfoWindow HTML
                    const categoryHtml = store.categories && store.categories.length > 0
                        ? `<div class="tag-badge">${escapeHtml(store.categories[0])}</div>`
                        : '';

                    const infoContent = `
                        <div class="map-infowindow">
                            <h3>${escapeHtml(store.name)}</h3>
                            ${categoryHtml}
                            <p>${escapeHtml(store.description ? store.description.substring(0, 120) + (store.description.length > 120 ? '...' : '') : '')}</p>
                            <a href="${store.profile_url}" class="btn-visit-pin">
                                Ver Loja <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    `;

                    const infoWindow = new google.maps.InfoWindow({
                        content: infoContent,
                    });

                    marker.addListener('click', () => {
                        if (activeInfoWindow) activeInfoWindow.close();
                        infoWindow.open(googleMap, marker);
                        activeInfoWindow = infoWindow;
                    });

                    mapMarkers.push(marker);
                });

                // Fit map bounds to encompass all stores
                if (mapMarkers.length > 0) {
                    googleMap.fitBounds(bounds, { top: 60, right: 60, bottom: 60, left: 60 });
                }

                // Initialize MarkerClusterer
                if (window.markerClusterer && window.markerClusterer.MarkerClusterer) {
                    new markerClusterer.MarkerClusterer({
                        map: googleMap,
                        markers: mapMarkers,
                    });
                }
            } catch (err) {
                console.error("Erro ao carregar Google Maps:", err);
                if (loadingIndicator) {
                    loadingIndicator.innerHTML = '<span class="text-danger fw-semibold"><i class="fas fa-circle-exclamation me-2"></i>Não foi possível carregar o mapa. Verifique a chave de API.</span>';
                }
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        // Google Maps API bootstrap loader
        (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
            key: MAPS_API_KEY,
            v: "weekly"
        });

        window.addEventListener('load', () => {
            initGoogleMap();
        });
    </script>
    @endpush
</x-market-layout>

