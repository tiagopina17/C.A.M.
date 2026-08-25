<body>
    <!-- Shared market/carimbo auth theme lives in meusestilos.css
         (.auth-page-wrapper, .badge-stamp, .sam override, .auth-card,
         .auth-title, .auth-subtitle, .form-label-stamp, .form-control-mkt,
         .auth-alert, .btn-mkt-submit). Only this page's wizard/step/
         predictions/ghost-button styles and size overrides stay here. -->
    <style>
    .auth-card{ max-width: 620px; }
    .auth-title{ font-size: clamp(1.6rem, 3vw, 2rem); }
    .auth-subtitle{ margin-bottom: 24px; }

    #messages-container:empty{ display:none; }
    #messages-container:not(:empty){ margin-bottom: 1.2rem; }

    /* ---------- Step indicator: carimbo ticket stubs ---------- */
    .step-indicator{
      display:flex;
      justify-content:center;
      align-items:center;
      gap:14px;
      margin-bottom:2rem;
    }
    .step-indicator::before{ content:none; }
    .step{
      width:42px;
      height:42px;
      border-radius:50%;
      background: var(--paper);
      border:2px solid var(--ink);
      display:flex;
      align-items:center;
      justify-content:center;
      font-family:'Space Mono', monospace;
      font-weight:700;
      color: var(--muted);
      transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .step.active{
      background: var(--accent-1);
      border-color: var(--accent-2);
      color:#fff;
    }
    .step.completed{
      background: var(--ochre);
      border-color: var(--ochre-deep);
      color:#241705;
    }

    .step-content{ display:none; }
    .step-content.active{ display:block; }

    .step-content h4{
      font-family:'Bricolage Grotesque', sans-serif;
      font-weight:700;
      font-size:1.25rem;
      color: var(--ink);
      margin-bottom:6px;
    }
    .step-content > p.text-muted{
      color: var(--muted) !important;
      font-size:.94rem;
      margin-bottom:22px !important;
    }

    .form-control-mkt.is-invalid{ border-color:#b3411f; }
    .form-control-mkt.is-valid{ border-color: var(--accent-1); }
    textarea.form-control-mkt{ resize: vertical; min-height: 90px; }

    /* ---------- Location search / predictions dropdown ---------- */
    .search-container{ position:relative; max-width:100%; }

    .predictions-container{
      position:absolute;
      left:0; right:0; top:100%;
      margin-top:10px;
      background: var(--card);
      border-radius:12px;
      overflow:hidden;
      border:2px solid var(--ink);
      box-shadow:0 10px 26px rgba(24,43,32,0.14);
      z-index:1200;
      max-height:220px;
      overflow-y:auto;
      display:none;
    }
    .prediction-item{
      padding:12px 16px;
      cursor:pointer;
      border-bottom:1px dashed var(--line);
      transition: background .12s ease;
    }
    .prediction-item:last-child{ border-bottom:0; }
    .prediction-item:hover{ background: rgba(15,122,66,0.08); }
    .prediction-main{ font-weight:600; color: var(--ink); font-size:.94rem; }
    .prediction-secondary{ font-size:.82rem; color: var(--muted); }

    .selected-place-info{
      margin-top:14px;
      padding:12px 14px;
      background: var(--paper-2);
      border:2px dashed var(--accent-1);
      border-radius:10px;
      color: var(--ink);
      font-size:.9rem;
      display:none;
    }
    .place-id-display{ color: var(--muted); }

    /* ---------- Buttons: this page's submit can be disabled mid-wizard,
       so it needs :not(:disabled)-qualified state rules on top of the
       shared .btn-mkt-submit base in meusestilos.css. ---------- */
    .btn-mkt-submit:hover:not(:disabled){
      background:#0d6b3a;
      color:#fff;
      transform: translate(-2px,-2px);
      box-shadow: 6px 6px 0 var(--accent-2);
    }
    .btn-mkt-submit:active:not(:disabled){ transform:translate(0,0); box-shadow:2px 2px 0 var(--accent-2); }
    .btn-mkt-submit:disabled{ opacity:.55; cursor:not-allowed; box-shadow:4px 4px 0 var(--accent-2); }

    .btn-mkt-ghost{
      background: transparent;
      color: var(--ink);
      border:2px solid var(--ink);
      border-radius:999px;
      padding:13px 22px;
      font-family:'Bricolage Grotesque', sans-serif;
      font-weight:700;
      transition: background .15s ease, color .15s ease;
    }
    .btn-mkt-ghost:hover{ background: var(--ink); color:#fff; }
    </style>

    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="text-center">
                <span class="badge-stamp"><i class="fas fa-store"></i>Selo · nova loja</span>
            </div>
            <h2 class="auth-title">Regista a tua <span class="sam">Loja</span></h2>
            <p class="auth-subtitle">Preenche o formulário abaixo para registares a tua loja na plataforma.</p>

            <!-- Error/Success Messages -->
            <div id="messages-container"></div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step-1">1</div>
                <div class="step" id="step-2">2</div>
            </div>

            <!-- Step 1: Store Info -->
            <div class="step-content active" id="content-1">
                <h4>Informações da Loja</h4>
                <p class="text-muted">Insira os dados da sua loja.</p>

                <div class="mb-3">
                    <label for="nomeloja" class="form-label-stamp">Nome da loja</label>
                    <input type="text" class="form-control form-control-mkt" id="nomeloja" placeholder="Insira o nome da loja" required>
                    <div class="valid-feedback">Tudo certo!</div>
                    <div class="invalid-feedback">Este campo é obrigatório.</div>
                </div>

                <div class="mb-4">
                    <label for="descricao" class="form-label-stamp">Descrição da loja</label>
                    <textarea class="form-control form-control-mkt" id="descricao" rows="3" placeholder="Descreva brevemente a sua loja"></textarea>
                    <div class="valid-feedback">Tudo certo!</div>
                </div>

                <div class="d-grid">
                    <button class="btn-mkt-submit" id="next-step-1">Continuar</button>
                </div>
            </div>

            <!-- Step 2: Location -->
            <div class="step-content" id="content-2">
                <h4>Localização da Loja</h4>
                <p class="text-muted">Encontre e selecione a localização da sua loja.</p>

                <div class="mb-4">
                    <label for="location-search" class="form-label-stamp">Procurar localização</label>
                    <div class="search-container">
                        <input type="text" class="form-control form-control-mkt" id="location-search" placeholder="Digite o nome ou morada da sua loja..." autocomplete="off" required>
                        <div id="predictions-container" class="predictions-container"></div>
                        <div class="valid-feedback">Tudo certo!</div>
                        <div class="invalid-feedback">Este campo é obrigatório.</div>
                    </div>
                    <div id="selected-place-info" class="selected-place-info">
                        <strong>Local selecionado:</strong> <span id="selected-place-name"></span><br>
                        <small class="place-id-display">Place ID: <span id="selected-place-id"></span></small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn-mkt-ghost" id="prev-step-2">Voltar</button>
                    <button class="btn-mkt-submit flex-fill" id="complete-btn" disabled>
                        <span id="submit-text">Registar</span>
                        <span id="submit-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PHP section to output the API key from .env -->
    <script>
        <?php
        // Load environment variables
        function loadEnv($path) {
            if (!file_exists($path)) {
                return;
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }

        // Load the .env file from parent directory
        loadEnv(__DIR__ . '/../.env');
        $mapsApiKey = getenv('API_KEY') ?: '';
        ?>
        const GOOGLE_MAPS_API_KEY = "<?php echo htmlspecialchars($mapsApiKey); ?>";
    </script>

    <script>
        // Form data storage
        let formData = {
            nomeloja: '',
            descricao: '',
            place_id: '',
            place_name: ''
        };

        // Google Places variables
        let autocompleteService;
        let sessionToken;
        let selectedPlace = null;
        let googleMapsLoaded = false;

        // Google Maps API loading functions
        function loadGoogleMapsAPI() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places&callback=initAutocomplete`;
            script.async = true;
            script.defer = true;
            script.onerror = function() {
                initFallbackAutocomplete();
            };
            document.head.appendChild(script);
        }

        // Global error handlers for Google Maps API
        window.gm_authFailure = function() {
            initFallbackAutocomplete();
        };

        // Initialize Google Places API
        function initAutocomplete() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                initFallbackAutocomplete();
                return;
            }

            try {
                autocompleteService = new google.maps.places.AutocompleteService();
                sessionToken = new google.maps.places.AutocompleteSessionToken();
                googleMapsLoaded = true;

                const input = document.getElementById('location-search');
                setupLocationInput(input);
            } catch (error) {
                initFallbackAutocomplete();
            }
        }

        // Make initAutocomplete globally accessible for Google Maps callback
        window.initAutocomplete = initAutocomplete;

        // Fallback for when Google Maps API fails
        function initFallbackAutocomplete() {
            googleMapsLoaded = false;

            const input = document.getElementById('location-search');
            setupLocationInput(input);
        }

        function setupLocationInput(input) {
            let debounceTimer;

            input.addEventListener('input', function() {
                const query = this.value.trim();

                if (formData.place_id && query !== formData.place_name) {
                    clearSelectedPlace();
                }

                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    hidePredictions();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    if (googleMapsLoaded) {
                        getPlacePredictions(query);
                    } else {
                        getMockPredictions(query);
                    }
                }, 300);
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    clearSelectedPlace();
                }
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-container')) {
                    hidePredictions();
                }
            });
        }

        // Real Google Places API predictions
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

        // Mock predictions fallback
        function getMockPredictions(query) {
            const mockPredictions = [
                {
                    place_id: 'mock_place_1',
                    description: `${query} - Centro Comercial`,
                    structured_formatting: {
                        main_text: `${query} - Centro Comercial`,
                        secondary_text: 'Guimarães, Portugal'
                    }
                },
                {
                    place_id: 'mock_place_2',
                    description: `${query} - Rua Principal`,
                    structured_formatting: {
                        main_text: `${query} - Rua Principal`,
                        secondary_text: 'Braga, Portugal'
                    }
                }
            ];

            displayPredictions(mockPredictions);
        }

        function clearSelectedPlace() {
            formData.place_id = '';
            formData.place_name = '';
            selectedPlace = null;
            document.getElementById('selected-place-info').style.display = 'none';
            document.getElementById('complete-btn').disabled = true;
            const input = document.getElementById('location-search');
            input.classList.remove('is-valid');
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

            showPredictions();
        }

        function selectPlace(prediction) {
            const input = document.getElementById('location-search');

            input.value = prediction.description;

            formData.place_id = prediction.place_id;
            formData.place_name = prediction.description;
            selectedPlace = prediction;

            document.getElementById('selected-place-name').textContent = prediction.description;
            document.getElementById('selected-place-id').textContent = prediction.place_id;
            document.getElementById('selected-place-info').style.display = 'block';
            document.getElementById('complete-btn').disabled = false;

            input.classList.remove('is-invalid');
            input.classList.add('is-valid');

            hidePredictions();

            if (googleMapsLoaded) {
                sessionToken = new google.maps.places.AutocompleteSessionToken();
            }
        }

        function showPredictions() {
            document.getElementById('predictions-container').style.display = 'block';
        }

        function hidePredictions() {
            document.getElementById('predictions-container').style.display = 'none';
        }

        // Step navigation
        function nextStep(currentStep) {
            if (validateStep(currentStep)) {
                saveStepData(currentStep);

                document.getElementById(`content-${currentStep}`).classList.remove('active');
                document.getElementById(`step-${currentStep}`).classList.remove('active');
                document.getElementById(`step-${currentStep}`).classList.add('completed');

                const nextStepNum = currentStep + 1;
                document.getElementById(`content-${nextStepNum}`).classList.add('active');
                document.getElementById(`step-${nextStepNum}`).classList.add('active');
            }
        }

        function prevStep(currentStep) {
            document.getElementById(`content-${currentStep}`).classList.remove('active');
            document.getElementById(`step-${currentStep}`).classList.remove('active');

            const prevStepNum = currentStep - 1;
            document.getElementById(`content-${prevStepNum}`).classList.add('active');
            document.getElementById(`step-${prevStepNum}`).classList.remove('completed');
            document.getElementById(`step-${prevStepNum}`).classList.add('active');
        }

        function validateStep(step) {
            const inputs = document.querySelectorAll(`#content-${step} input[required], #content-${step} textarea[required]`);
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });

            if (step === 2) {
                const locationInput = document.getElementById('location-search');

                if (!formData.place_id || !locationInput.value.trim()) {
                    locationInput.classList.add('is-invalid');
                    locationInput.classList.remove('is-valid');
                    isValid = false;
                } else {
                    locationInput.classList.remove('is-invalid');
                    locationInput.classList.add('is-valid');
                }
            }

            return isValid;
        }

        function saveStepData(step) {
            switch(step) {
                case 1:
                    const nomelojaEl = document.getElementById('nomeloja');
                    const descricaoEl = document.getElementById('descricao');

                    formData.nomeloja = nomelojaEl ? nomelojaEl.value : '';
                    formData.descricao = descricaoEl ? descricaoEl.value : '';
                    break;
            }
        }

        function completeRegistration() {
            if (!validateStep(2)) {
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('complete-btn');
            const submitText = document.getElementById('submit-text');
            const submitSpinner = document.getElementById('submit-spinner');

            submitBtn.disabled = true;
            submitText.textContent = 'A processar...';
            submitSpinner.style.display = 'inline-block';

            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = './scripts/sc_registo_lojas.php';
            form.style.display = 'none';

            // Add form data
            Object.keys(formData).forEach(key => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = formData[key];
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners for buttons
            const nextBtn = document.getElementById('next-step-1');
            const prevBtn = document.getElementById('prev-step-2');
            const completeBtn = document.getElementById('complete-btn');

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    nextStep(1);
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    prevStep(2);
                });
            }

            if (completeBtn) {
                completeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    completeRegistration();
                });
            }

            // Add real-time validation
            const inputs = document.querySelectorAll('input[required], textarea[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // Load Google Maps API or use fallback
            if (GOOGLE_MAPS_API_KEY && GOOGLE_MAPS_API_KEY.length > 0) {
                loadGoogleMapsAPI();
            } else {
                initFallbackAutocomplete();
            }
        });
    </script>
</body>
</html>