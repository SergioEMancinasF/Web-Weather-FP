<?php
$apiKey = '150efedab0cb302c8ca99ea13dc5d44b'; // Your actual API key
$lat = 52.5015022;
$lon = 13.4065913;

$weatherApiUrl = "https://api.openweathermap.org/data/2.5/onecall?lat={$lat}&lon={$lon}&units=metric&appid={$apiKey}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $weatherApiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$weatherResponse = curl_exec($ch);
if ($weatherResponse === false) {
    $error_msg = curl_error($ch);
    error_log("Failed to fetch weather data: $error_msg");
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Failed to fetch weather data']);
    exit;
}
curl_close($ch);
error_log("Weather data: $weatherResponse");
echo $weatherResponse;
?>
