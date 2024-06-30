<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page - SkyCast</title>
    <link rel="stylesheet" href="./assets/styles/styles.css" />
    <link rel="icon" href="./assets/images/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header>
        <div id="header" class="container">
            <div class="logo-title-container">
                <img src="./assets/images/logo.png" alt="SkyCast Logo" class="logo">
                <h1>SkyCast Weather</h1>
            </div>
            <nav>
                <ul class="nav-links ">
                    <li><a href="homePage.php" class="active">Home</a></li>
                    <li><a href="aboutUs.html">About Us</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </nav>
            <div class="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </div>
    </header>
    <main>
        <div id="city-input" class="city-input container">
            <div class="search-box">
                <input type="text" id="city" placeholder="Enter city name" autocomplete="off">
                <button id="getWeather">Get Weather</button>
                <div class="dropdown-content" id="dropdown-content"></div>
            </div>
            <button id="useCurrentLocation" class="use-current-location">Use Current Location</button>
        </div>
       
        <div class="locationInfo container" id="locationInfo">
            <h2 class="text-center" id="location"></h2>
            <div class="temp">
                <span class="temp-value" id="temp"></span>
                <span class="temp-icon">
                    <img src="./assets/images/cloudy.png" id="currentIcon" alt="Weather Icon"/>
                </span>
            </div>

            <div class="additionalInfo">
                <div id="condition"></div>
                <div id="overview"></div>
                <div id="feels_like"></div>
                <div id="pressure"></div>
                <div id="humidity"></div>
                <div id="cloudiness"></div>
                <div id="sunrise"></div>
                <div id="sunset"></div>
            </div>

            <div id="forecast" class="forecast"></div>
        </div>

        <div class="unit-toggle">
            <button id="switchC" class="active">°C</button>
            <button id="switchF">°F</button>
        </div>
        
        <div class="chart-container">
            <canvas id="temperatureChart24" style="margin-top: 20px;"></canvas>
            <canvas id="temperatureChart5Days" style="margin-top: 20px;"></canvas>
        </div>
    </main>
    <footer class="footerWrapper">
        <div class="footer-content">
            <div class="footer-text">
                <p>&copy; 2024 SkyCast Weather. All rights reserved.</p>
            </div>
            <div class="footer-nav">
                <ul>
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="aboutUs.html">About Us</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </footer>
     <script>
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.burger');
            const navLinks = document.querySelector('.nav-links');

            burger.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>
    <script defer>
        document.addEventListener("DOMContentLoaded", function () {
            const cityInput = document.getElementById('city');
            const dropdownContent = document.getElementById('dropdown-content');
            const temperatureChart24 = document.getElementById('temperatureChart24').getContext('2d');
            const temperatureChart5Days = document.getElementById('temperatureChart5Days').getContext('2d');
            const locationInfo = document.getElementById('locationInfo');
            
            let currentCity = '';
            let currentUnits = 'metric';
            let currentCoords = null;
            let chart24, chart5Days;

            function getWeatherIcon(condition) {
                switch (condition.toLowerCase()) {
                    case 'clear':
                    case 'sunny':
                        return './assets/images/sunny.png';
                    case 'clouds':
                    case 'cloudy':
                        return './assets/images/cloudy.png';
                    case 'rain':
                    case 'rainy':
                        return './assets/images/rain.png';
                    case 'thunderstorm':
                    case 'stormy':
                        return './assets/images/stormy.png';
                    case 'night':
                        return './assets/images/night.png';
                    case 'wind':
                        return './assets/images/wind.png';
                    case 'cloudynight':
                        return './assets/images/cloudynight.png';
                    case 'mist':
                    case 'fog':
                        return './assets/images/cloudy.png';
                    case 'snow':
                        return './assets/images/cloudy.png';
                    default:
                        return './assets/images/sunny.png';
                }
            }

            document.getElementById('switchC').addEventListener('click', function() {
                currentUnits = 'metric';
                updateWeatherData();
                updateButtonStyles();
            });

            document.getElementById('switchF').addEventListener('click', function() {
                currentUnits = 'imperial';
                updateWeatherData();
                updateButtonStyles();
            });

            function updateWeatherData() {
                if (currentCity) {
                    fetchWeather({city: currentCity}, currentUnits);
                } else if (currentCoords) {
                    fetchWeather(currentCoords, currentUnits);
                }
            }

            function updateButtonStyles() {
                document.getElementById('switchC').classList.toggle('active', currentUnits === 'metric');
                document.getElementById('switchF').classList.toggle('active', currentUnits === 'imperial');
            }

            cityInput.addEventListener('input', function () {
                const query = cityInput.value.trim();
                if (query) {
                    fetchCitySuggestions(query);
                } else {
                    dropdownContent.innerHTML = '';
                }
            });

            document.getElementById('getWeather').addEventListener('click', function () {
                const city = cityInput.value.trim();
                if (city) {
                    currentCity = city;
                    currentCoords = null;
                    fetchWeather({city}, currentUnits);
                }
            });

            document.getElementById('useCurrentLocation').addEventListener('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const coords = {
                            lat: position.coords.latitude,
                            lon: position.coords.longitude
                        };
                        currentCoords = coords;
                        currentCity = '';
                        fetchWeather(coords, currentUnits);
                    }, function (error) {
                        alert('Error getting location: ' + error.message);
                    });
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            });

            function fetchCitySuggestions(query) {
                axios.get(`fetchCitySuggestions.php?query=${query}`)
                    .then(response => {
                        const cities = response.data;
                        dropdownContent.innerHTML = cities.map(city => `<div>${city.name}, ${city.country}</div>`).join('');
                        dropdownContent.querySelectorAll('div').forEach(div => {
                            div.addEventListener('click', function () {
                                cityInput.value = this.textContent;
                                dropdownContent.innerHTML = '';
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching city suggestions:', error);
                    });
            }

            function fetchWeather(params, units) {
                const query = params.city ? `city=${encodeURIComponent(params.city)}` : `lat=${params.lat}&lon=${params.lon}`;
                axios.get(`fetchWeather.php?${query}&units=${units}`)
                    .then(response => {
                        const data = response.data;
                        locationInfo.style.display = 'block';
                        displayWeather(data.current);
                        displayForecast(data.forecast);
                        displayOverview(data.forecast);
                        displayTemperatureCharts(data.forecast);
                    })
                    .catch(error => {
                        console.error('Error fetching the weather data:', error);
                        alert('Failed to fetch weather data. Please check the console for more details.');
                    });
            }

            function displayWeather(data) {
                const weatherContainer = document.querySelector('.locationInfo');
                weatherContainer.querySelector('#location').textContent = `My Location: ${data.name}, ${data.sys.country}`;
                weatherContainer.querySelector('#temp').textContent = `${data.main.temp.toFixed(2)}°${currentUnits === 'metric' ? 'C' : 'F'}`;
                weatherContainer.querySelector('#currentIcon').src = getWeatherIcon(data.weather[0].main);
                weatherContainer.querySelector('#condition').innerHTML = `<strong>${new Date().toLocaleString()}</strong><br>Condition: ${data.weather[0].description}`;
                
                // Add the new weather details
                document.getElementById('feels_like').textContent = `Feels Like: ${data.main.feels_like.toFixed(2)}°${currentUnits === 'metric' ? 'C' : 'F'}`;
                document.getElementById('pressure').textContent = `Pressure: ${data.main.pressure} hPa`;
                document.getElementById('humidity').textContent = `Humidity: ${data.main.humidity}%`;
                document.getElementById('cloudiness').textContent = `Cloudiness: ${data.clouds.all}%`;
                document.getElementById('sunrise').textContent = `Sunrise: ${new Date(data.sys.sunrise * 1000).toLocaleTimeString()}`;
                document.getElementById('sunset').textContent = `Sunset: ${new Date(data.sys.sunset * 1000).toLocaleTimeString()}`;
            }

            function displayForecast(data) {
                const forecastContainer = document.querySelector('#forecast');
                forecastContainer.innerHTML = data.list.slice(0, 5).map(item => `
                    <div>
                        <p>${new Date(item.dt * 1000).toLocaleString()}</p>
                        <p>Temp: ${item.main.temp.toFixed(2)}°${currentUnits === 'metric' ? 'C' : 'F'}</p>
                        <p>Condition: ${item.weather[0].description}</p>
                        <p>AQI: ${getAqiDescription(item.aqi)}</p>
                        <img src="${getWeatherIcon(item.weather[0].main)}" alt="Weather Icon"/>
                    </div>
                `).join('');
            }

            function displayOverview(data) {
                const overviewContainer = document.querySelector('#overview');
                const averageTemp = data.list.slice(0, 5).reduce((sum, item) => sum + item.main.temp, 0) / 5;
                const averageAQI = data.list.slice(0, 5).reduce((sum, item) => sum + (item.aqi || 0), 0) / 5;
                overviewContainer.textContent = `Average Temperature: ${averageTemp.toFixed(2)}°${currentUnits === 'metric' ? 'C' : 'F'}, Air Quality: ${getAqiDescription(averageAQI)}`;
            }

            function getAqiDescription(aqi) {
                switch (Math.round(aqi)) {
                    case 1:
                        return 'Good';
                    case 2:
                        return 'Fair';
                    case 3:
                        return 'Moderate';
                    case 4:
                        return 'Poor';
                    case 5:
                        return 'Very Poor';
                    default:
                        return 'Unknown';
                }
            }

            function displayTemperatureCharts(data) {
                const labels24 = data.list.slice(0, 24).map(item => new Date(item.dt * 1000).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
                const tempData24 = data.list.slice(0, 24).map(item => item.main.temp);

                const labels5Days = data.list.filter((_, index) => index % 8 === 0).map(item => new Date(item.dt * 1000).toLocaleDateString());
                const tempData5Days = data.list.filter((_, index) => index % 8 === 0).map(item => item.main.temp);

                const annotations24 = getDayAnnotations(data.list.slice(0, 24));
                const annotations5Days = getDayAnnotations(data.list.filter((_, index) => index % 8 === 0));

                const currentDateTime = new Date();
                const currentDateTimeIndex24 = labels24.findIndex(label => new Date(`1970-01-01T${label}:00`).getTime() >= currentDateTime.getTime());

                annotations24[`currentDateTime`] = {
                    type: 'line',
                    xMin: currentDateTimeIndex24,
                    xMax: currentDateTimeIndex24,
                    borderColor: 'blue',
                    borderWidth: 2,
                    label: {
                        content: 'Now',
                        enabled: true,
                        position: 'top'
                    }
                };

                if (chart24) chart24.destroy();
                if (chart5Days) chart5Days.destroy();

                chart24 = new Chart(temperatureChart24, {
                    type: 'line',
                    data: {
                        labels: labels24,
                        datasets: [{
                            label: '24-hour Temperature',
                            data: tempData24,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            annotation: {
                                annotations: annotations24
                            },
                            title: {
                                display: true,
                                text: '24-hour Temperature Forecast'
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Time'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: `Temperature (°${currentUnits === 'metric' ? 'C' : 'F'})`
                                }
                            }
                        }
                    }
                });

                chart5Days = new Chart(temperatureChart5Days, {
                    type: 'line',
                    data: {
                        labels: labels5Days,
                        datasets: [{
                            label: '5-day Temperature Forecast',
                            data: tempData5Days,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            },
                            annotation: {
                                annotations: annotations5Days
                            },
                            title: {
                                display: true,
                                text: '5-day Temperature Forecast'
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Date'
                                }
                            },
                            y: {
                                display: true,
                                title: {
                                    display: true,
                                    text: `Temperature (°${currentUnits === 'metric' ? 'C' : 'F'})`
                                }
                            }
                        }
                    }
                });
            }

            function getDayAnnotations(data) {
                const annotations = {};
                let currentDate = new Date(data[0].dt * 1000).getDate();

                data.forEach((item, index) => {
                    const itemDate = new Date(item.dt * 1000).getDate();
                    if (itemDate !== currentDate) {
                        currentDate = itemDate;
                        annotations[`line${index}`] = {
                            type: 'line',
                            xMin: index,
                            xMax: index,
                            borderColor: 'red',
                            borderWidth: 2,
                            label: {
                                content: `Day ${new Date(item.dt * 1000).toLocaleDateString()}`,
                                enabled: true,
                                position: 'top'
                            }
                        };
                    }
                });

                return annotations;
            }
        });
    </script>
</body>
</html>
