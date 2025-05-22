<?php
// Fetch JSON data from the API
$api_url = 'https://json.geoapi.pt/cp/5050-092';
$json_data = file_get_contents($api_url);

// Check if the API call was successful
if ($json_data === FALSE) {
    die('Error occurred while fetching JSON data.');
}

// Decode the JSON data into a PHP array
$data = json_decode($json_data, true);

// Check if the JSON decoding was successful
if ($data === NULL) {
    die('Error occurred while decoding JSON data.');
}

// Pass the data to JavaScript
$json_for_js = json_encode($data);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Square from Coordinates</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 600px; }
    </style>
</head>
<body>
<div id="map"></div>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Get JSON data from PHP
    var jsonData = <?php echo $json_for_js; ?>;

    // Display JSON data in the HTML
    var apiDataDiv = document.getElementById('apiData');
    var coordsstring = jsonData['poligono'];
    // Convert the string to an array of coordinates
    const coordinates = coordsString.split(',').map(Number);
    const coordsArray = [];
    for (let i = 0; i < coordinates.length; i += 2) {
        coordsArray.push([coordinates[i], coordinates[i + 1]]);
    }

    // Find the topmost, bottommost, leftmost, and rightmost points
    const topmost = Math.max(...coordsArray.map(coord => coord[0]));
    const bottommost = Math.min(...coordsArray.map(coord => coord[0]));
    const leftmost = Math.min(...coordsArray.map(coord => coord[1]));
    const rightmost = Math.max(...coordsArray.map(coord => coord[1]));

    // Calculate the center of the bounding box
    const centerLat = (topmost + bottommost) / 2;
    const centerLon = (leftmost + rightmost) / 2;

    // Determine the side length of the square (max of height and width of the bounding box)
    const sideLength = Math.max(topmost - bottommost, rightmost - leftmost);

    // Calculate the coordinates of the square's vertices
    const square = [
        [centerLat + sideLength / 2, centerLon - sideLength / 2],  // Top-left
        [centerLat + sideLength / 2, centerLon + sideLength / 2],  // Top-right
        [centerLat - sideLength / 2, centerLon + sideLength / 2],  // Bottom-right
        [centerLat - sideLength / 2, centerLon - sideLength / 2],  // Bottom-left
        [centerLat + sideLength / 2, centerLon - sideLength / 2]   // Closing the square
    ];

    // Initialize the map
    const map = L.map('map').setView([centerLat, centerLon], 13);

    // Add a tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Add the square to the map
    const polygon = L.polygon(square, { color: 'blue' }).addTo(map);

    // Fit the map to the square
    map.fitBounds(polygon.getBounds());
</script>
</body>
</html>
