<!DOCTYPE html>
<html>
<head>
    <title>Google Maps - Lojas</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        #map {
            height: calc(100% - 60px);
            width: 100%;
        }

        .info {
            padding: 15px;
            background-color: #f0f0f0;
            border-bottom: 2px solid #4285F4;
        }

        /* Custom marker styles */
        .store-marker {
            background-color: #4285F4;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .store-marker:hover {
            transform: scale(1.2);
        }

        /* Info window styles */
        .info-window {
            font-family: Arial, sans-serif;
            max-width: 200px;
        }

        .info-window h3 {
            margin: 0 0 10px 0;
            color: #4285F4;
        }

        .info-window p {
            margin: 5px 0;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="info">
        <strong>Mapa de Lojas</strong> - 
        <span id="store-count">Loading...</span>
        <span id="error-msg" style="color: red; margin-left: 15px;"></span>
    </div>
    
    <div id="map"></div>

    <script>
        <?php
        // Include database connection
        require_once __DIR__ . '/../connections/connection.php';
        
        // Load environment variables for Google Maps API key
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
        
        loadEnv(__DIR__ . '/../.env');
        $mapsApiKey = getenv('API_KEY') ?: '';
        
        $errorMsg = '';
        $lojas = [];
        
        try {
            // Use your custom connection function
            $conn = new_db_connection();
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Fetch stores data from lojas table
            $stmt = $conn->query("
                SELECT 
                    id_Loja,
                    nome_loja,
                    descricao,
                    place_id,
                    lat,
                    lon,
                    imgperfil,
                    inicio
                FROM lojas
                ORDER BY nome_loja
            ");
            
            $lojas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($lojas)) {
                $errorMsg = 'Nenhuma loja encontrada na base de dados';
            }
            
            // Close connection
            $conn = null;
            
        } catch (PDOException $e) {
            $lojas = [];
            $errorMsg = 'Erro de conexão: ' . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
        ?>
        
        const GOOGLE_MAPS_API_KEY = "<?php echo htmlspecialchars($mapsApiKey); ?>";
        const LOJAS_DATA = <?php echo json_encode($lojas); ?>;
        const ERROR_MSG = "<?php echo htmlspecialchars($errorMsg); ?>";
    </script>

    <!-- Load MarkerClusterer library separately -->
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

    <script>
        // Display error message if any
        if (ERROR_MSG) {
            document.getElementById('error-msg').textContent = ERROR_MSG;
        }
        
        // Debug: log the data
        console.log('Lojas data:', LOJAS_DATA);
        console.log('Total lojas:', LOJAS_DATA.length);
        
        let map;
        let markers = [];
        let infoWindows = [];
        let clusterer;

        async function initMap() {
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
            
            // Create map centered on Portugal
            map = new Map(document.getElementById('map'), {
                center: { lat: 39.572958, lng: -8.052576 },
                zoom: 6.3,
                mapId: '4504f8b37365c3d0',
            });

            // Update store count
            document.getElementById('store-count').textContent = 
                `${LOJAS_DATA.length} lojas encontradas`;

            // Add markers for each store
            LOJAS_DATA.forEach((loja, index) => {
                const lat = parseFloat(loja.lat);
                const lng = parseFloat(loja.lon);
                
                if (isNaN(lat) || isNaN(lng)) {
                    console.warn(`Invalid coordinates for store: ${loja.nome_loja}`);
                    return;
                }

                // Create custom marker element
                const markerContent = document.createElement('div');
                markerContent.className = 'store-marker';
                markerContent.textContent = (index + 1).toString();

                // Create marker
                const marker = new AdvancedMarkerElement({
                    map,
                    position: { lat, lng },
                    title: loja.nome_loja,
                    collisionBehavior: google.maps.CollisionBehavior.OPTIONAL_AND_HIDES_LOWER_PRIORITY,
                });

                // Create info window content
                const infoContent = document.createElement('div');
                infoContent.className = 'info-window';
                infoContent.innerHTML = `
                    <h3>${loja.nome_loja}</h3>
                    ${loja.descricao ? `<p>${loja.descricao}</p>` : ''}
                    ${loja.imgperfil ? `<img src="${loja.imgperfil}" alt="${loja.nome_loja}" style="max-width: 100%; margin-top: 10px; border-radius: 4px;">` : ''}
                    <p style="font-size: 11px; color: #666; margin-top: 10px;">ID: ${loja.place_id}</p>
                `;

                // Create info window
                const infoWindow = new google.maps.InfoWindow({
                    content: infoContent,
                });

                // Add click listener
                marker.addListener('click', () => {
                    // Close all other info windows
                    infoWindows.forEach(iw => iw.close());
                    // Open this info window
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
                infoWindows.push(infoWindow);
            });

            // Initialize MarkerClusterer using the external library
            // Note: MarkerClusterer from @googlemaps/markerclusterer package
            clusterer = new markerClusterer.MarkerClusterer({
                map,
                markers,
                algorithm: new markerClusterer.SuperClusterAlgorithm({
                    radius: 100,
                    maxZoom: 15,
                }),
                renderer: {
                    render: ({ count, position }) => {
                        const color = count > 10 ? "#ea4335" : count > 5 ? "#fbbc04" : "#4285F4";
                        const size = count > 10 ? 60 : count > 5 ? 50 : 40;
                        const fontSize = count > 10 ? 18 : count > 5 ? 16 : 14;
                        
                        return new google.maps.Marker({
                            position,
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                fillColor: color,
                                fillOpacity: 0.9,
                                strokeColor: "#fff",
                                strokeWeight: 3,
                                scale: size / 2,
                            },
                            label: {
                                text: String(count),
                                color: "#fff",
                                fontSize: `${fontSize}px`,
                                fontWeight: "bold",
                            },
                            title: `Cluster de ${count} lojas`,
                            zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count,
                        });
                    },
                },
            });

            // Adjust map bounds to fit all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                LOJAS_DATA.forEach(loja => {
                    bounds.extend({
                        lat: parseFloat(loja.lat),
                        lng: parseFloat(loja.lon)
                    });
                });
                map.fitBounds(bounds);
                
                // Add some padding
                const padding = { top: 50, right: 50, bottom: 50, left: 50 };
                map.fitBounds(bounds, padding);
            }
        }

        // Google Maps API loader
        (g=>{
            var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;
            b=b[c]||(b[c]={});
            var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{
                await (a=m.createElement("script"));
                e.set("libraries",[...r]+"");
                for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);
                e.set("callback",c+".maps."+q);
                a.src=`https://maps.${c}apis.com/maps/api/js?`+e;
                d[q]=f;
                a.onerror=()=>h=n(Error(p+" could not load."));
                a.nonce=m.querySelector("script[nonce]")?.nonce||"";
                m.head.append(a)
            }));
            d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))
        })({
            key: GOOGLE_MAPS_API_KEY,
            v: "weekly"
        });

        // Initialize when page loads
        window.addEventListener('load', () => {
            setTimeout(initMap, 500);
        });
    </script>
</body>
</html>