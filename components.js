import axios from 'axios';



document.addEventListener("DOMContentLoaded", function () {
    const getWeatherButton = document.getElementById('getWeather');
    const useCurrentLocationButton = document.getElementById('useCurrentLocation');

    getWeatherButton.addEventListener('click', function () {
        const city = document.getElementById('city').value.trim();
        if (city) {
            fetchWeather(city);
        }
    });

    useCurrentLocationButton.addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                fetchWeatherByCoordinates(position.coords.latitude, position.coords.longitude);
            }, function (error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    });

    function fetchWeather(city) {
        axios.get(`fetchWeather.php?city=${city}`)
            .then(response => {
                const data = response.data;
                if (!data || data.cod !== 200) {
                    alert('Failed to fetch weather data: ' + (data.message || 'Unknown error'));
                    return;
                }
                displayWeather(data);
            })
            .catch(error => {
                console.error('Error fetching the weather data:', error);
                alert('Failed to fetch weather data. Please check the console for more details.');
            });
    }

    function fetchWeatherByCoordinates(lat, lon) {
        axios.get(`fetchWeather.php?lat=${lat}&lon=${lon}`)
            .then(response => {
                const data = response.data;
                if (!data || data.cod !== 200) {
                    alert('Failed to fetch weather data: ' + (data.message || 'Unknown error'));
                    return;
                }
                displayWeather(data);
            })
            .catch(error => {
                console.error('Error fetching the weather data:', error);
                alert('Failed to fetch weather data. Please check the console for more details.');
            });
    }

    function displayWeather(data) {
        const weatherContainer = document.querySelector('.locationInfo');
        weatherContainer.innerHTML = `
            <h2 id="location">My Location: ${data.name}, ${data.sys.country}</h2>
            <div class="temp">
                <span class="temp-value" id="temp">${data.main.temp.toFixed(2)}°C</span>
                <span class="temp-icon">
                    <img src="./assets/${data.weather[0].icon}.png" id="currentIcon" alt="Weather Icon"/>
                </span>
            </div>
            <p id="condition">Condition: ${data.weather[0].description}</p>
        `;
    }
});
