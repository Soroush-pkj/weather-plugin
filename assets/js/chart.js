document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('weather-chart-canvas').getContext('2d');
    let weatherChart;

    // Function to fetch data and update the chart
    const updateChart = (cityName) => {
        fetch(`https://api.openweathermap.org/data/2.5/forecast?q=${cityName}&appid=4503f87f2a76fb1b5c028df33323cf5c&units=metric`)
            .then(response => response.json())
            .then(data => {
                if (data.list) {
                    const labels = data.list.slice(0, 5).map(entry => new Date(entry.dt * 1000).toLocaleDateString());
                    const temperatures = data.list.slice(0, 5).map(entry => entry.main.temp);

                    if (weatherChart) {
                        weatherChart.destroy();
                    }

                    weatherChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `5-Day Temperature Trend for ${cityName}`,
                                data: temperatures,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                },
                            },
                        },
                    });
                } else {
                    console.error('No data available for the provided city.');
                }
            })
            .catch(error => console.error('Error fetching weather data:', error));
    };

    // Determine the initial city
    const initializeChart = () => {
        let selectedCities = JSON.parse(localStorage.getItem('selected-cities')) || [];
        let initialCity = selectedCities.length > 0 ? selectedCities[0] : document.querySelector('.weather-city h3').textContent;
        updateChart(initialCity);
    };

    // Event listener for submit button
    document.getElementById('submit-cities').addEventListener('click', () => {
        let selectedCities = JSON.parse(localStorage.getItem('selected-cities')) || [];
        if (selectedCities.length > 0) {
            updateChart(selectedCities[0]);
        }
    });

    // Initialize the chart on page load
    initializeChart();
});
