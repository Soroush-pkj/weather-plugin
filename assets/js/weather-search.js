document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('#weather-search');
    const resultsContainer = document.querySelector('#search-results');
    const selectedCitiesContainer = document.querySelector('#selected-cities');
    const submitButton = document.querySelector('#submit-cities');
    const weatherContainer = document.querySelector('.weather-container');
    const maxCities = 5;

    let selectedCities = [];

    // جستجوی شهرها از API
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();

        if (query.length < 3) return;

        fetch(`https://api.openweathermap.org/data/2.5/find?q=${query}&appid=4503f87f2a76fb1b5c028df33323cf5c&type=like&units=metric`)
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.list) {
                    data.list.forEach(city => {
                        const cityName = `${city.name}, ${city.sys.country}`;
                        const cityItem = document.createElement('div');
                        cityItem.textContent = cityName;
                        cityItem.classList.add('city-item');

                        cityItem.addEventListener('click', function () {
                            if (selectedCities.length < maxCities && !selectedCities.includes(cityName)) {
                                selectedCities.push(cityName);

                                const selectedCity = document.createElement('div');
                                selectedCity.textContent = cityName;
                                selectedCity.classList.add('selected-city');
                                selectedCitiesContainer.appendChild(selectedCity);

                                // پاک کردن نتایج جستجو
                                resultsContainer.innerHTML = '';
                                searchInput.value = '';
                            } else {
                                alert(`You can select up to ${maxCities} cities.`);
                            }
                        });

                        resultsContainer.appendChild(cityItem);
                    });
                }
            })
            .catch(error => console.error('Error fetching cities:', error));
    });

    // ارسال شهرهای انتخاب‌شده به سرور
    submitButton.addEventListener('click', function (e) {
        e.preventDefault();

        if (selectedCities.length > 0) {
            fetch(weatherSearch.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'weather_update_cities',
                    selected_cities: JSON.stringify(selectedCities),
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const newCities = data.data;
                        weatherContainer.innerHTML = ''; // پاک کردن نتایج قبلی

                        newCities.forEach(city => {
                            const cityDiv = document.createElement('div');
                            cityDiv.classList.add('weather-city');

                            cityDiv.innerHTML = `
                                <h3>${city.city}</h3>
                                <img src="${city.icon}" alt="Weather icon">
                                <p>Temperature: ${city.temp}°C</p>
                                <p>${city.description}</p>
                            `;
                            weatherContainer.appendChild(cityDiv);
                        });

                        selectedCities = []; // ریست کردن لیست
                        selectedCitiesContainer.innerHTML = '';
                    }
                })
                .catch(() => alert('Failed to fetch weather data. Please try again.'));
        } else {
            alert('Please select at least one city.');
        }
    });
});
