<x-market-layout title="Registar Loja — Cá do Bairro">
    <div class="auth-page-wrapper">
        <div class="auth-card position-relative overflow-hidden shadow" style="max-width: 620px;">
            <div style="position: absolute; top: 0; left: 0; right: 0; height: 6px; background: repeating-linear-gradient(90deg, var(--accent-1) 0 18px, var(--ochre) 18px 36px);"></div>

            <div class="text-center pt-2">
                <span class="badge-stamp">
                    <i class="fa-solid fa-store"></i>Selo · nova loja
                </span>
            </div>

            <h1 class="auth-title">Regista a tua <span style="color: var(--accent-1);">Loja</span></h1>
            <p class="auth-subtitle">Preencha o formulário abaixo para registar o seu negócio no bairro.</p>

            <!-- Errors Alert (Single error) -->
            @if ($errors->any())
                <div class="auth-alert alert-danger mb-3 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation fs-5 flex-shrink-0"></i>
                    <div class="fw-semibold" style="font-size: 0.95rem;">
                        {{ $errors->first() }}
                    </div>
                </div>
            @endif

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step-1">1</div>
                <div class="step" id="step-2">2</div>
            </div>

            <form action="{{ route('stores.store') }}" method="POST" id="store-reg-form">
                @csrf

                <!-- Hidden place inputs -->
                <input type="hidden" id="place_id" name="place_id" value="{{ old('place_id') }}">
                <input type="hidden" id="place_name" name="place_name" value="{{ old('place_name') }}">
                <input type="hidden" id="lat" name="lat" value="{{ old('lat') }}">
                <input type="hidden" id="lon" name="lon" value="{{ old('lon') }}">

                <!-- Step 1: Store Info -->
                <div class="step-content active" id="content-1">
                    <h3 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; color: var(--ink);">
                        Informações da Loja
                    </h3>
                    <p class="text-muted small mb-4">Insira a identificação do seu estabelecimento comercial.</p>

                    <div class="mb-3">
                        <label for="name" class="form-label-stamp">Nome da Loja <span class="text-danger">*</span></label>
                        <input type="text"
                               class="market-input @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Ex.: Padaria do Bairro"
                               required
                               autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label-stamp">Descrição da Loja</label>
                        <textarea class="market-input @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Conte brevemente sobre o seu negócio, tradição ou produtos frescos...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn-mkt-submit w-100 border-0" id="next-step-1">
                            Continuar para Localização
                        </button>
                    </div>
                </div>

                <!-- Step 2: Location Search -->
                <div class="step-content" id="content-2">
                    <h3 class="fw-bold mb-1" style="font-family: 'Bricolage Grotesque', sans-serif; font-size: 1.25rem; color: var(--ink);">
                        Localização da Loja
                    </h3>
                    <p class="text-muted small mb-4">Procure a morada ou o nome do seu estabelecimento no mapa.</p>

                    <div class="mb-4">
                        <label for="location-search" class="form-label-stamp">Procurar Morada / Estabelecimento <span class="text-danger">*</span></label>
                        <div class="search-container">
                            <input type="text"
                                   class="market-input"
                                   id="location-search"
                                   placeholder="Escreva a morada ou nome da loja..."
                                   autocomplete="off">
                            <div id="predictions-container" class="predictions-container shadow"></div>
                        </div>

                        <div id="selected-place-info" class="selected-place-info">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fa-solid fa-location-dot text-success"></i>
                                <strong>Local Selecionado:</strong>
                            </div>
                            <div id="selected-place-name" class="fw-semibold"></div>
                            <small class="place-id-display">Place ID: <span id="selected-place-id"></span></small>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn-mkt-ghost" id="prev-step-2">
                            Voltar
                        </button>
                        <button type="submit" class="btn-mkt-submit flex-fill border-0" id="complete-btn" disabled>
                            <span id="submit-text">Concluir Registo</span>
                            <span id="submit-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const GOOGLE_MAPS_API_KEY = @json($googleMapsApiKey ?? '');

        // Form data storage
        let storeData = {
            name: '',
            description: '',
            place_id: '',
            place_name: '',
            lat: '',
            lon: ''
        };

        // Google Places variables
        let autocompleteService;
        let placesService;
        let sessionToken;
        let googleMapsLoaded = false;

        function loadGoogleMapsAPI() {
            if (!GOOGLE_MAPS_API_KEY) {
                initFallbackAutocomplete();
                return;
            }

            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places&callback=initStoreAutocomplete`;
            script.async = true;
            script.defer = true;
            script.onerror = function() {
                initFallbackAutocomplete();
            };
            document.head.appendChild(script);
        }

        window.gm_authFailure = function() {
            initFallbackAutocomplete();
        };

        function initStoreAutocomplete() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                initFallbackAutocomplete();
                return;
            }

            try {
                autocompleteService = new google.maps.places.AutocompleteService();
                sessionToken = new google.maps.places.AutocompleteSessionToken();
                placesService = new google.maps.places.PlacesService(document.createElement('div'));
                googleMapsLoaded = true;

                setupLocationInput();
            } catch (error) {
                initFallbackAutocomplete();
            }
        }
        window.initStoreAutocomplete = initStoreAutocomplete;

        function initFallbackAutocomplete() {
            googleMapsLoaded = false;
            setupLocationInput();
        }

        function setupLocationInput() {
            const input = document.getElementById('location-search');
            if (!input) return;

            let debounceTimer;

            input.addEventListener('input', function() {
                const query = this.value.trim();

                if (storeData.place_id && query !== storeData.place_name) {
                    clearSelectedPlace();
                }

                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    hidePredictions();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    if (googleMapsLoaded && autocompleteService) {
                        getPlacePredictions(query);
                    } else {
                        getMockPredictions(query);
                    }
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-container')) {
                    hidePredictions();
                }
            });
        }

        function getPlacePredictions(query) {
            const request = {
                input: query,
                sessionToken: sessionToken,
                componentRestrictions: { country: 'pt' }
            };

            autocompleteService.getPlacePredictions(request, (predictions, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                    displayPredictions(predictions);
                } else {
                    hidePredictions();
                }
            });
        }

        function getMockPredictions(query) {
            const mockPredictions = [
                {
                    place_id: 'mock_place_1',
                    description: `${query} — Mercado Central de Lisboa`,
                    lat: 38.7071,
                    lon: -9.1466,
                    structured_formatting: {
                        main_text: `${query} — Mercado Central`,
                        secondary_text: 'Lisboa, Portugal'
                    }
                },
                {
                    place_id: 'mock_place_2',
                    description: `${query} — Praça do Comércio Local`,
                    lat: 41.1496,
                    lon: -8.6109,
                    structured_formatting: {
                        main_text: `${query} — Praça do Comércio Local`,
                        secondary_text: 'Porto, Portugal'
                    }
                }
            ];

            displayPredictions(mockPredictions);
        }

        function displayPredictions(predictions) {
            const container = document.getElementById('predictions-container');
            container.innerHTML = '';

            predictions.forEach(prediction => {
                const item = document.createElement('div');
                item.className = 'prediction-item';
                item.innerHTML = `
                    <div class="prediction-main">${prediction.structured_formatting.main_text}</div>
                    <div class="prediction-secondary">${prediction.structured_formatting.secondary_text || ''}</div>
                `;

                item.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    selectPlace(prediction);
                });

                container.appendChild(item);
            });

            container.style.display = 'block';
        }

        function selectPlace(prediction) {
            const input = document.getElementById('location-search');
            input.value = prediction.description;

            storeData.place_id = prediction.place_id;
            storeData.place_name = prediction.description;

            document.getElementById('place_id').value = prediction.place_id;
            document.getElementById('place_name').value = prediction.description;

            if (prediction.lat && prediction.lon) {
                storeData.lat = prediction.lat;
                storeData.lon = prediction.lon;
                document.getElementById('lat').value = prediction.lat;
                document.getElementById('lon').value = prediction.lon;
            } else if (googleMapsLoaded && placesService) {
                // Fetch geometry coordinates directly
                placesService.getDetails({
                    placeId: prediction.place_id,
                    fields: ['geometry']
                }, (place, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK && place?.geometry?.location) {
                        const lat = place.geometry.location.lat();
                        const lon = place.geometry.location.lng();
                        storeData.lat = lat;
                        storeData.lon = lon;
                        document.getElementById('lat').value = lat;
                        document.getElementById('lon').value = lon;
                    }
                });
            }

            document.getElementById('selected-place-name').textContent = prediction.description;
            document.getElementById('selected-place-id').textContent = prediction.place_id;
            document.getElementById('selected-place-info').style.display = 'block';
            document.getElementById('complete-btn').disabled = false;

            hidePredictions();

            if (googleMapsLoaded && google.maps.places.AutocompleteSessionToken) {
                sessionToken = new google.maps.places.AutocompleteSessionToken();
            }
        }

        function clearSelectedPlace() {
            storeData.place_id = '';
            storeData.place_name = '';
            storeData.lat = '';
            storeData.lon = '';

            document.getElementById('place_id').value = '';
            document.getElementById('place_name').value = '';
            document.getElementById('lat').value = '';
            document.getElementById('lon').value = '';

            document.getElementById('selected-place-info').style.display = 'none';
            document.getElementById('complete-btn').disabled = true;
        }

        function hidePredictions() {
            const container = document.getElementById('predictions-container');
            if (container) container.style.display = 'none';
        }

        // Wizard navigation
        document.addEventListener('DOMContentLoaded', function() {
            const nextBtn = document.getElementById('next-step-1');
            const prevBtn = document.getElementById('prev-step-2');
            const nameInput = document.getElementById('name');
            const form = document.getElementById('store-reg-form');

            // Restore place if old input existed
            const oldPlaceId = document.getElementById('place_id').value;
            const oldPlaceName = document.getElementById('place_name').value;
            if (oldPlaceId && oldPlaceName) {
                document.getElementById('location-search').value = oldPlaceName;
                document.getElementById('selected-place-name').textContent = oldPlaceName;
                document.getElementById('selected-place-id').textContent = oldPlaceId;
                document.getElementById('selected-place-info').style.display = 'block';
                document.getElementById('complete-btn').disabled = false;
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    if (!nameInput.value.trim()) {
                        nameInput.classList.add('is-invalid');
                        nameInput.focus();
                        return;
                    }
                    nameInput.classList.remove('is-invalid');

                    // Transition to step 2
                    document.getElementById('content-1').classList.remove('active');
                    document.getElementById('step-1').classList.remove('active');
                    document.getElementById('step-1').classList.add('completed');

                    document.getElementById('content-2').classList.add('active');
                    document.getElementById('step-2').classList.add('active');

                    document.getElementById('location-search').focus();
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    document.getElementById('content-2').classList.remove('active');
                    document.getElementById('step-2').classList.remove('active');

                    document.getElementById('step-1').classList.remove('completed');
                    document.getElementById('step-1').classList.add('active');
                    document.getElementById('content-1').classList.add('active');
                });
            }

            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = document.getElementById('complete-btn');
                    const submitText = document.getElementById('submit-text');
                    const submitSpinner = document.getElementById('submit-spinner');

                    submitBtn.disabled = true;
                    submitText.textContent = 'A registar loja...';
                    submitSpinner.style.display = 'inline-block';
                });
            }

            loadGoogleMapsAPI();
        });
    </script>
    @endpush
</x-market-layout>

