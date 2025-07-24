<!DOCTYPE html>
<html>
<head>
    <title>Google Maps Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        
        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        #map {
            height: 400px;
            width: 100%;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <h3>My Google Maps Demo</h3>
    
    <div class="info">
        <strong>Environment loaded:</strong> API key successfully retrieved from environment variables.
    </div>
    
    <!-- The div element for the map -->
    <div id="map"></div>

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

    <!-- Google Maps API Script -->
    <script>
        // Initialize and add the map
        let map;

        async function initMap() {
            try {
                // Request needed libraries
                const { Map } = await google.maps.importLibrary("maps");
                const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

                // Center the map on Australia
                const center = { lat: -25.0, lng: 133.0 };

                // Create the map, centered on Australia
                map = new Map(document.getElementById("map"), {
                    zoom: 5,
                    center: center,
                    mapId: "DEMO_MAP_ID",
                });

                // Define multiple locations with markers
                const locations = [
                    {
                        position: { lat: -25.344, lng: 131.031 },
                        title: "Uluru",
                        description: "Sacred Aboriginal site in Northern Territory"
                    },
                    {
                        position: { lat: -33.8688, lng: 151.2093 },
                        title: "Sydney Opera House",
                        description: "Iconic performing arts venue in Sydney"
                    },
                    {
                        position: { lat: -37.8136, lng: 144.9631 },
                        title: "Melbourne",
                        description: "Cultural capital of Australia"
                    },
                    {
                        position: { lat: -27.4698, lng: 153.0251 },
                        title: "Brisbane",
                        description: "Subtropical capital of Queensland"
                    },
                    {
                        position: { lat: -31.9505, lng: 115.8605 },
                        title: "Perth",
                        description: "Western Australia's largest city"
                    },
                    {
                        position: { lat: -34.9285, lng: 138.6007 },
                        title: "Adelaide",
                        description: "City of Churches in South Australia"
                    },
                    {
                        position: { lat: -12.4634, lng: 130.8456 },
                        title: "Darwin",
                        description: "Tropical capital of Northern Territory"
                    },
                    {
                        position: { lat: -42.8821, lng: 147.3272 },
                        title: "Hobart",
                        description: "Historic capital of Tasmania"
                    },
                    {
                        position: { lat: -35.2809, lng: 149.1300 },
                        title: "Canberra",
                        description: "Capital city of Australia"
                    },
                    {
                        position: { lat: -23.6980, lng: 133.8807 },
                        title: "Alice Springs",
                        description: "Heart of the Australian Outback"
                    }
                ];

                // Create markers for each location
                locations.forEach((location, index) => {
                    const marker = new AdvancedMarkerElement({
                        map: map,
                        position: location.position,
                        title: location.title,
                    });

                    // Create info window content
                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div style="padding: 10px; max-width: 200px;">
                                <h3 style="margin: 0 0 10px 0; color: #333;">${location.title}</h3>
                                <p style="margin: 0; color: #666; font-size: 14px;">${location.description}</p>
                                <p style="margin: 5px 0 0 0; color: #999; font-size: 12px;">
                                    Lat: ${location.position.lat.toFixed(4)}, 
                                    Lng: ${location.position.lng.toFixed(4)}
                                </p>
                            </div>
                        `
                    });

                    // Add click listener to show info window
                    marker.addListener("click", () => {
                        // Close any open info windows
                        if (window.currentInfoWindow) {
                            window.currentInfoWindow.close();
                        }
                        
                        infoWindow.open({
                            anchor: marker,
                            map: map,
                        });
                        
                        // Keep track of current info window
                        window.currentInfoWindow = infoWindow;
                    });
                });
                
                console.log(`Map loaded successfully with ${locations.length} markers!`);
                
            } catch (error) {
                console.error("Error initializing map:", error);
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

        // Initialize when page loads and Google Maps is ready
        window.addEventListener('load', () => {
            // Wait a bit for the API to be fully loaded
            setTimeout(initMap, 500);
        });
    </script>
</body>
</html> 