<?php

class Weather_Chart {

    private $api_key = '4503f87f2a76fb1b5c028df33323cf5c';

    public function __construct() {
        add_shortcode('weather-chart', [ $this, 'render_weather_chart' ]);
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_chart_assets' ]);
    }

    
    public function enqueue_chart_assets() {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }

    // Render the weather chart shortcode
    public function render_weather_chart() {
        ob_start();
        ?>
        <div id="weather-chart-container">
            <canvas id="weather-chart" width="800" height="400"></canvas>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const apiKey = '<?php echo $this->api_key; ?>';
            let chartInstance = null;

            // Function to get the unit symbol based on the browser language
            function getUnitSymbol() {
                const browserLanguage = navigator.language || navigator.userLanguage;
                return browserLanguage.startsWith('fa') ? '\u00B0C' : '\u00B0F';
            }

            const unitSymbol = getUnitSymbol();
            const units = unitSymbol === '\u00B0C' ? 'metric' : 'imperial';

            function loadChart(cityName) {
                // Fetch historical weather data for the city
                fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${encodeURIComponent(cityName)}&appid=${apiKey}&units=${units}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data || !data.list) {
                            console.error('Invalid data received from weather API.');
                            return;
                        }

                        // Process the data to extract daily temperatures
                        const temperatures = [];
                        const labels = [];
                        const uniqueDays = new Set();

                        data.list.forEach(entry => {
                            const date = new Date(entry.dt_txt);
                            const dayLabel = date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });

                            if (!uniqueDays.has(dayLabel)) {
                                uniqueDays.add(dayLabel);
                                labels.push(dayLabel);
                                temperatures.push(entry.main.temp);
                            }
                        });

                        const limitedTemperatures = temperatures.slice(0, 5);
                        const limitedLabels = labels.slice(0, 5);

                        
                        if (chartInstance) {
                            chartInstance.destroy();
                        }

                        // Render the chart
                        const ctx = document.getElementById('weather-chart').getContext('2d');
                        chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: limitedLabels,
                                datasets: [{
                                    label: `Temperature Trend for ${cityName}`,
                                    data: limitedTemperatures,
                                    backgroundColor: 'rgba(87, 143, 143, 0.2)',
                                    borderColor: 'rgb(100, 226, 226)',
                                    borderWidth: 1,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Days',
                                        },
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: `Temperature (${unitSymbol})`,
                                        },
                                        beginAtZero: true,
                                    },
                                },
                            },
                        });
                    })
                    .catch(error => console.error('Error fetching weather chart data:', error));
            }

            // Initialize chart with the first city
            function initializeChart() {
                const firstCityElement = document.querySelector('.weather-city h3');
                if (firstCityElement) {
                    const cityName = firstCityElement.textContent.trim();
                    loadChart(cityName);
                } else {
                    console.error('No city found to display weather chart.');
                }
            }

            // Observe changes in .weather-container
            const weatherContainer = document.querySelector('.weather-container');

            if (weatherContainer) {
                const observer = new MutationObserver(mutations => {
                    mutations.forEach(mutation => {
                        if (mutation.type === 'childList' || mutation.type === 'attributes') {
                            const updatedCityElement = document.querySelector('.weather-city h3');
                            if (updatedCityElement) {
                                const updatedCityName = updatedCityElement.textContent.trim();
                                loadChart(updatedCityName);
                            }
                        }
                    });
                });

                observer.observe(weatherContainer, {
                    childList: true,
                    attributes: true,
                    subtree: true
                });
            }

            
            initializeChart();
        });
        </script>

        <?php
        return ob_get_clean();
    }
}


new Weather_Chart();
