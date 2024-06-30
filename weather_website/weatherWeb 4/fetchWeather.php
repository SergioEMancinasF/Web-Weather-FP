<?php
// fetchWeather.php

$apiKey = '150efedab0cb302c8ca99ea13dc5d44b'; // Your actual API key

$city = isset($_GET['city']) ? urlencode($_GET['city']) : null;
$lat = isset($_GET['lat']) ? urlencode($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? urlencode($_GET['lon']) : null;
$units = isset($_GET['units']) ? $_GET['units'] : 'metric'; 

if ($city) {
    $geoApiUrl = "http://api.openweathermap.org/geo/1.0/direct?q={$city}&limit=1&appid={$apiKey}";
    error_log("Fetching coordinates for city: $city");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geoApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $geoResponse = curl_exec($ch);
    if ($geoResponse === false) {
        $error_msg = curl_error($ch);
        error_log("Failed to fetch coordinates: $error_msg");
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Failed to fetch coordinates']);
        curl_close($ch);
        exit;
    }
    curl_close($ch);

    $geoData = json_decode($geoResponse, true);
    if (count($geoData) > 0) {
        $lat = $geoData[0]['lat'];
        $lon = $geoData[0]['lon'];
    } else {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'City not found']);
        exit;
    }
}

if ($lat && $lon) {
    $weatherApiUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units={$units}&appid={$apiKey}";
    $forecastApiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&units={$units}&appid={$apiKey}";
    $airQualityApiUrl = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
    error_log("Fetching weather, forecast, and air quality for coordinates: lat=$lat, lon=$lon, units=$units");
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'City or coordinates (lat, lon) must be provided.']);
    exit;
}

// Fetch current weather
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $weatherApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$currentWeatherResponse = curl_exec($ch);
if ($currentWeatherResponse === false) {
    $error_msg = curl_error($ch);
    error_log("Failed to fetch current weather data: $error_msg");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch current weather data']);
    curl_close($ch);
    exit;
}
curl_close($ch);
error_log("Current weather data: $currentWeatherResponse");

// Fetch forecast
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $forecastApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$forecastResponse = curl_exec($ch);
if ($forecastResponse === false) {
    $error_msg = curl_error($ch);
    error_log("Failed to fetch weather forecast data: $error_msg");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch weather forecast data']);
    curl_close($ch);
    exit;
}
curl_close($ch);
error_log("Forecast data: $forecastResponse");

// Fetch air quality
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $airQualityApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$airQualityResponse = curl_exec($ch);
if ($airQualityResponse === false) {
    $error_msg = curl_error($ch);
    error_log("Failed to fetch air quality data: $error_msg");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch air quality data']);
    curl_close($ch);
    exit;
}
curl_close($ch);
error_log("Air quality data: $airQualityResponse");

// Decode responses
$currentWeatherData = json_decode($currentWeatherResponse, true);
$forecastData = json_decode($forecastResponse, true);
$airQualityData = json_decode($airQualityResponse, true);

if ((isset($currentWeatherData['cod']) && $currentWeatherData['cod'] != 200) ||
    (isset($forecastData['cod']) && $forecastData['cod'] != '200')) {
    error_log("API response error: Current weather: " . $currentWeatherData['message'] . ", Forecast: " . $forecastData['message']);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $currentWeatherData['message'] ?? $forecastData['message']]);
    exit;
}

// Combine air quality data with forecast
foreach ($forecastData['list'] as &$forecast) {
    $closestAqi = null;
    $closestTimeDiff = PHP_INT_MAX;
    $forecastTime = $forecast['dt'];

    foreach ($airQualityData['list'] as $aq) {
        $timeDiff = abs($aq['dt'] - $forecastTime);
        if ($timeDiff < $closestTimeDiff) {
            $closestTimeDiff = $timeDiff;
            $closestAqi = $aq['main']['aqi'];
        }
    }
    $forecast['aqi'] = $closestAqi;
}

$responseData = [
    'current' => $currentWeatherData,
    'forecast' => $forecastData,
    'air_quality' => $airQualityData
];

header('Content-Type: application/json');
echo json_encode($responseData);
?>
