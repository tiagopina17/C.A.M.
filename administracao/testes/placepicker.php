<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Places Autocomplete - Place ID</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;  
            background-color: #f5f5f5;
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        #autocomplete-input {
            width: 100%;
            padding: 15px 20px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 25px;
            outline: none;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        #autocomplete-input:focus {
            border-color: #4285f4;
            box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.1);
        }

        .predictions-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .prediction-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .prediction-item:hover {
            background-color: #f8f9fa;
        }

        .prediction-item:last-child {
            border-bottom: none;
        }

        .prediction-main {
            font-weight: 500;
            color: #333;
        }

        .prediction-secondary {
            font-size: 14px;
            color: #666;
            margin-top: 2px;
        }

        .selected-place {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .place-id {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
            border: 1px solid #e9ecef;
        }

        .loading {
            padding: 15px 20px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .no-results {
            padding: 15px 20px;
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Google Places Autocomplete - Place ID - <span style="color: red;">INACTIVE</span></h1>
    
    <div class="search-container">
        <input 
            type="text" 
            id="autocomplete-input" 
            placeholder="Search for places..."
            autocomplete="off"
        >
        <div id="predictions-container" class="predictions-container"></div>
    </div>

    <div id="selected-place" class="selected-place" style="display: none;">
        <h3>Selected Place:</h3>
        <p><strong>Name:</strong> <span id="place-name"></span></p>
        <p><strong>Place ID:</strong></p>
        <div id="place-id" class="place-id"></div>
    </div>

    <script>
        let autocompleteService;
        let sessionToken;

        // Initialize the Google Places API
        function initAutocomplete() {
            autocompleteService = new google.maps.places.AutocompleteService();
            sessionToken = new google.maps.places.AutocompleteSessionToken();
            
            const input = document.getElementById('autocomplete-input');
            let debounceTimer;
            
            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(debounceTimer);
                
                if (query.length < 2) {
                    hidePredictions();
                    return;
                }
                
                // Debounce the API calls
                debounceTimer = setTimeout(() => {
                    getPlacePredictions(query);
                }, 300);
            });
            
            // Hide predictions when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-container')) {
                    hidePredictions();
                }
            });
        }

        function getPlacePredictions(query) {
            const request = {
                input: query,
                sessionToken: sessionToken
            };
            
            showLoading();
            
            autocompleteService.getPlacePredictions(request, (predictions, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && predictions) {
                    displayPredictions(predictions);
                } else {
                    showNoResults();
                }
            });
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
                
                item.addEventListener('click', () => {
                    selectPlace(prediction);
                });
                
                container.appendChild(item);
            });
            
            showPredictions();
        }

        function selectPlace(prediction) {
            const input = document.getElementById('autocomplete-input');
            input.value = prediction.description;
            hidePredictions();
            
            // Display the place ID
            displayPlaceId(prediction.description, prediction.place_id);
            
            // Create new session token for next search
            sessionToken = new google.maps.places.AutocompleteSessionToken();
        }

        function displayPlaceId(placeName, placeId) {
            document.getElementById('place-name').textContent = placeName;
            document.getElementById('place-id').textContent = placeId;
            document.getElementById('selected-place').style.display = 'block';
            
            // Log to console as well
            console.log('Selected Place:', placeName);
            console.log('Place ID:', placeId);
        }

        function showPredictions() {
            document.getElementById('predictions-container').style.display = 'block';
        }

        function hidePredictions() {
            document.getElementById('predictions-container').style.display = 'none';
        }

        function showLoading() {
            const container = document.getElementById('predictions-container');
            container.innerHTML = '<div class="loading">Searching...</div>';
            showPredictions();
        }

        function showNoResults() {
            const container = document.getElementById('predictions-container');
            container.innerHTML = '<div class="no-results">No results found</div>';
            showPredictions();
        }
    </script>

    <!-- Replace YOUR_API_KEY with your actual Google Maps API key -->
    <script src="https://maps.googleapis.com/maps/api/js?key=API_KEY&libraries=places&callback=initAutocomplete" async defer></script>
</body>
</html>