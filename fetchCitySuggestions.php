<?php
// fetchCitySuggestions.php

$apiKey = '150efedab0cb302c8ca99ea13dc5d44b'; // Your actual API key
$query = isset($_GET['query']) ? urlencode($_GET['query']) : null;

if ($query) {
    $apiUrl = "http://api.openweathermap.org/geo/1.0/direct?q={$query}&limit=10&appid={$apiKey}";
    error_log("Fetching city suggestions for query: $query");
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Query parameter must be provided.']);
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
if ($response === false) {
    $error_msg = curl_error($ch);
    error_log("Failed to fetch city suggestions: $error_msg");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch city suggestions']);
    exit;
}
curl_close($ch);

$suggestions = json_decode($response, true);

// Remove duplicate city names
$uniqueCities = [];
foreach ($suggestions as $city) {
    $cityName = $city['name'] . ', ' . $city['country'];
    if (!isset($uniqueCities[$cityName])) {
        $uniqueCities[$cityName] = $city;
    }
}

$responseData = array_values($uniqueCities);

header('Content-Type: application/json');
echo json_encode($responseData);
?>
