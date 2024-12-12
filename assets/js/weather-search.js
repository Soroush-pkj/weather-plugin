document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('#weather-search');
    const resultsContainer = document.querySelector('#search-results');
    const selectedCitiesContainer = document.querySelector('#selected-cities');
    const maxCities = 5;

    let selectedCities = [];

    // Fetch city suggestions from the API
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

                                // Remove city from results
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
});




jQuery(document).ready(function ($) {
    let selectedCities = []; // لیست شهرهای انتخاب‌شده

    // افزودن شهر به لیست
    $('#weather-add-city').on('click', function (e) {
        e.preventDefault();
        const city = $('#weather-search').val().trim();
        if (city && selectedCities.length < 5 && !selectedCities.includes(city)) {
            selectedCities.push(city);
            $('#selected-cities').append(`<li>${city}</li>`);
            $('#weather-search').val(''); // پاک کردن فیلد ورودی
        }
    });

    // ارسال شهرهای انتخاب‌شده
    $('#submit-cities').on('click', function (e) {
        e.preventDefault();
        if (selectedCities.length > 0) {
            $.ajax({
                url: weatherSearch.ajax_url,
                method: 'POST',
                data: {
                    action: 'weather_update_cities',
                    selected_cities: selectedCities,
                },
                success: function (response) {
                    if (response.success && response.data) {
                        const newCities = response.data;
                        $('#new-weather-cities').empty(); // پاک کردن نتایج قبلی
                        newCities.forEach(city => {
                            $('#new-weather-cities').append(`
                                <div class="weather-city">
                                    <h3>${city.city}</h3>
                                    <img src="${city.icon}" alt="Weather icon">
                                    <p>Temperature: ${city.temp}°C</p>
                                    <p>${city.description}</p>
                                </div>
                            `);
                        });
                        selectedCities = []; // پاک کردن لیست شهرها
                        $('#selected-cities').empty();
                    }
                },
                error: function () {
                    alert('Failed to fetch weather data. Please try again.');
                },
            });
        }
    });
});

