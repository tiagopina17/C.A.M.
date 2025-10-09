<body>
    <div class="container mt-5 mb-5">
        <div class="col-md-8 mx-auto">
            <div class="card shadow p-4">
                <h2 class="text-center sam mb-4">Regista a tua Loja</h2>
                <p class="text-center text-muted mb-4">Preenche o formulário abaixo para registares a tua loja na plataforma.</p>
                
                <!-- Error/Success Messages -->
                <div id="messages-container"></div>
                
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" id="step-1">1</div>
                    <div class="step" id="step-2">2</div>
                </div>
                
                <!-- Step 1: Store Info -->
                <div class="step-content active" id="content-1">
                    <h4 class="mb-3">Informações da Loja</h4>
                    <p class="text-muted mb-4">Insira os dados da sua loja.</p>
                    
                    <div class="mb-3">
                        <label for="nomeloja" class="form-label">Nome da loja:</label>
                        <input type="text" class="form-control" id="nomeloja" placeholder="Insira o nome da loja" required>
                        <div class="valid-feedback">Tudo certo!</div>
                        <div class="invalid-feedback">Este campo é obrigatório.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="descricao" class="form-label">Descrição da loja:</label>
                        <textarea class="form-control" id="descricao" rows="3" placeholder="Descreva brevemente a sua loja"></textarea>
                        <div class="valid-feedback">Tudo certo!</div>
                    </div>
                    
                    <div class="d-grid">
                        <button class="btn btn-outline-verde" id="next-step-1">Continuar</button>
                    </div>
                </div>
                
                <!-- Step 2: Location -->
                <div class="step-content" id="content-2">
                    <h4 class="mb-3">Localização da Loja</h4>
                    <p class="text-muted mb-4">Encontre e selecione a localização da sua loja.</p>
                    
                    <div class="mb-4">
                        <label for="location-search" class="form-label">Procurar localização:</label>
                        <div class="search-container">
                            <input type="text" class="form-control" id="location-search" placeholder="Digite o nome ou morada da sua loja..." autocomplete="off" required>
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
                        <button class="btn btn-secondary" id="prev-step-2">Voltar</button>
                        <button class="btn btn-outline-verde flex-fill" id="complete-btn" disabled>
                            <span id="submit-text">Registar</span>
                            <span id="submit-spinner" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                        </button>
                    </div>
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