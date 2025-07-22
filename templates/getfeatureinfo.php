<?php
require_once 'db.php';
require_once 'functions.php';

// Get settings
$settings = getSettings();
if (!$settings) {
    http_response_code(500);
    echo json_encode(['error' => 'Settings not configured']);
    exit;
}

$geoserverUrl = $settings['geoserver_url'];
$username = $settings['geoserver_username'];
$password = $settings['geoserver_password'];

// Get parameters
$layers = $_GET['layers'] ?? '';
$coordinate = $_GET['coordinate'] ?? '';
$mapWidth = intval($_GET['map_width'] ?? 800);
$mapHeight = intval($_GET['map_height'] ?? 600);
$mapBbox = $_GET['map_bbox'] ?? '';
$pixelX = intval($_GET['pixel_x'] ?? 0);
$pixelY = intval($_GET['pixel_y'] ?? 0);

if (empty($layers) || empty($coordinate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Parse coordinate (expecting "lon,lat")
$coordParts = explode(',', $coordinate);
if (count($coordParts) !== 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid coordinate format']);
    exit;
}

$lon = floatval($coordParts[0]);
$lat = floatval($coordParts[1]);

// Construct WMS GetFeatureInfo request matching the working implementation
$wmsParams = [
    'REQUEST' => 'GetFeatureInfo',
    'SERVICE' => 'WMS',
    'VERSION' => '1.3.0',
    'LAYERS' => $layers,
    'QUERY_LAYERS' => $layers,
    'INFO_FORMAT' => 'application/json',
    'FEATURE_COUNT' => 10,
    'EXCEPTIONS' => 'XML',
    'STYLES' => '',
    'FORMAT' => 'image/png',
    'TRANSPARENT' => 'true',
    'CRS' => 'EPSG:3857',
    'I' => $pixelX,
    'J' => $pixelY,
    'WIDTH' => $mapWidth,
    'HEIGHT' => $mapHeight,
    'BBOX' => $mapBbox,
    'BUFFER' => 10
];

// Build query string
$queryString = http_build_query($wmsParams);

// Construct the full URL
$url = $geoserverUrl . '?' . $queryString;

// Ensure we're using the WMS service endpoint
if (!str_contains($geoserverUrl, '/wms')) {
    $url = rtrim($geoserverUrl, '/') . '/wms?' . $queryString;
}

// Initialize cURL
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => 'GeoShare/1.0',
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json'
    ]
]);

// Add authentication if provided
if (!empty($username) && !empty($password)) {
    curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
}

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$error = curl_error($ch);
curl_close($ch);

// Debug information
$debug = [
    'version' => 'NEW FILE - v2.0',
    'url' => $url,
    'http_code' => $httpCode,
    'content_type' => $contentType,
    'response_length' => strlen($response),
    'layers' => $layers,
    'coordinate' => $coordinate,
    'map_size' => $mapWidth . 'x' . $mapHeight,
    'pixel' => $pixelX . ',' . $pixelY,
    'bbox' => $mapBbox,
    'raw_response_preview' => substr($response, 0, 200)
];

// Check for cURL errors
if ($error) {
    http_response_code(500);
    echo json_encode([
        'error' => 'cURL error: ' . $error,
        'debug' => $debug
    ]);
    exit;
}

// Check HTTP status
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode([
        'error' => "HTTP error: $httpCode",
        'debug' => $debug,
        'response' => $response
    ]);
    exit;
}

// Try to parse JSON response
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid JSON response: ' . json_last_error_msg(),
        'debug' => $debug,
        'response' => substr($response, 0, 500)
    ]);
    exit;
}

// Check for WMS exceptions
if (isset($data['exceptionCode'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'WMS Exception: ' . ($data['exceptionText'] ?? $data['message'] ?? 'Unknown error'),
        'debug' => $debug
    ]);
    exit;
}

// Return the response with debug info
echo json_encode([
    'features' => $data['features'] ?? [],
    'totalFeatures' => $data['totalFeatures'] ?? 0,
    'debug' => $debug
]);
?> 