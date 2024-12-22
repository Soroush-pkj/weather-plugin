document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector("#weather-search");
  const resultsContainer = document.querySelector("#search-results");
  const selectedCitiesContainer = document.querySelector("#selected-cities");
  const submitButton = document.querySelector("#submit-cities");
  const weatherContainer = document.querySelector(".weather-container");
  const maxCities = 5;
  const loadingIndicator = document.querySelector("#loading-indicator");

  let selectedCities = [];
  let unitSymbol = getUnitSymbol();

  weatherContainer.style.display = "none";
  loadingIndicator.style.display = "none";

  selectedCitiesContainer.innerHTML = "";

  if (localStorage.getItem("selected-cities")) {
    selectedCities = JSON.parse(localStorage.getItem("selected-cities"));

    fetchWeatherDataForCities(selectedCities);

    // clear button
    const clearButton = document.createElement("button");
    clearButton.id = "clear-localstorage";
    clearButton.textContent = "Clear";
    selectedCitiesContainer.appendChild(clearButton);

    clearButton.addEventListener("click", () => {
      localStorage.clear();
      selectedCities = [];
      selectedCitiesContainer.innerHTML = "";
      searchInput.disabled = false;
      searchInput.placeholder = "Search for a city";
      searchInput.style.backgroundColor = "";
    });

    // show cities
    selectedCities.forEach((city) => {
      const selectedCity = document.createElement("div");
      selectedCity.textContent = city;
      selectedCity.classList.add("selected-city");
      selectedCitiesContainer.appendChild(selectedCity);
    });
  } else {
    fetchWeatherDataForCities(["Tehran", "New York", "Sydney"]);
  }

  // Search API
  searchInput.addEventListener("input", function () {
    const query = searchInput.value.trim();

    resultsContainer.style.display = "block";

    if (query.length < 3) {
      resultsContainer.innerHTML = '<p class="info">At least 3 Characters</p>';
      return;
    }

    resultsContainer.innerHTML = '<p class="info">Searching...</p>';

    fetch(
      `https://api.openweathermap.org/data/2.5/find?q=${query}&appid=4503f87f2a76fb1b5c028df33323cf5c&type=like&units=metric`
    )
      .then((response) => response.json())
      .then((data) => {
        resultsContainer.innerHTML = "";
        if (data.list && data.list.length > 0) {
          data.list.forEach((city) => {
            const cityName = `${city.name}, ${city.sys.country}`;

            // select already exist
            if (selectedCities.includes(cityName)) {
              return;
            }

            const cityItem = document.createElement("div");
            cityItem.textContent = cityName;
            cityItem.classList.add("city-item");

            cityItem.addEventListener("click", function () {
              if (selectedCities.includes(cityName)) {
                alert("This item already selected");
                return;
              }

              if (selectedCities.length < maxCities) {
                selectedCities.push(cityName);

                const selectedCity = document.createElement("div");
                selectedCity.textContent = cityName;
                selectedCity.classList.add("selected-city");
                selectedCitiesContainer.appendChild(selectedCity);

                resultsContainer.innerHTML = "";
                searchInput.value = "";

                if (selectedCities.length === maxCities) {
                  searchInput.disabled = true;
                  searchInput.placeholder = "You selected the maximum item";
                  searchInput.style.backgroundColor = "rgba(255, 0, 0, 0.1)";
                }
              }
            });

            resultsContainer.appendChild(cityItem);
          });
        } else {
          resultsContainer.innerHTML = '<p class="info">No Result</p>';
        }
      })
      .catch((error) => {
        console.error("Error fetching cities:", error);
        resultsContainer.innerHTML =
          '<p class="info">Failed to fetch cities</p>';
      });
  });

  // close result box by ckick other area
  document.addEventListener("click", function (e) {
    if (
      !searchInput.contains(e.target) &&
      !resultsContainer.contains(e.target)
    ) {
      resultsContainer.style.display = "none";
    }
  });

  // send city to server
  submitButton.addEventListener("click", function (e) {
    e.preventDefault();

    if (selectedCities.length > 0) {
      loadingIndicator.style.display = "block";

      localStorage.setItem("selected-cities", JSON.stringify(selectedCities));

      // send to server
      fetch(weatherSearch.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          action: "weather_update_cities",
          selected_cities: JSON.stringify(selectedCities),
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success && data.data) {
            const newCities = data.data;
            weatherContainer.innerHTML = "";

            newCities.forEach((city) => {
              const cityDiv = document.createElement("div");
              cityDiv.classList.add("weather-city");

              cityDiv.innerHTML = `
                                <h3>${city.city}</h3>
                                <img src="${city.icon}" alt="Weather icon">
                                <p>Temperature: ${city.temp}${unitSymbol}</p>
                                <p>${city.description}</p>
                            `;
              weatherContainer.appendChild(cityDiv);
            });

            loadingIndicator.style.display = "none";
            weatherContainer.style.display = "flex";
          } else {
            throw new Error("Invalid data from server");
          }
        })
        .catch((error) => {
          console.error("Error fetching weather data:", error);
          alert("Failed to fetch weather data. Please try again.");
          loadingIndicator.style.display = "none";
        });
    } else {
      alert("Please select at least one city.");
    }
  });

  function getUnitSymbol() {
    const browserLanguage = navigator.language || navigator.userLanguage;
    return browserLanguage.startsWith("fa") ? "°C" : "°F";
  }

  function fetchWeatherDataForCities(cities) {
    loadingIndicator.style.display = "block";

    fetch(weatherSearch.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams({
        action: "weather_update_cities",
        selected_cities: JSON.stringify(cities),
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.data) {
          weatherContainer.innerHTML = "";

          data.data.forEach((city) => {
            const cityDiv = document.createElement("div");
            cityDiv.classList.add("weather-city");

            cityDiv.innerHTML = `
                            <h3>${city.city}</h3>
                            <img src="${city.icon}" alt="Weather icon">
                            <p>Temperature: ${city.temp}${unitSymbol}</p>
                            <p>${city.description}</p>
                        `;
            weatherContainer.appendChild(cityDiv);
          });

          loadingIndicator.style.display = "none";
          weatherContainer.style.display = "flex";
        } else {
          throw new Error("Invalid data from server");
        }
      })
      .catch((error) => {
        console.error("Error fetching weather data:", error);
        alert("Failed to fetch weather data. Please try again.");

        loadingIndicator.style.display = "none";
      });
  }
});
