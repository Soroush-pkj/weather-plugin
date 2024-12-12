<?php

class Weather_API {
    private $weather_cache;

    public function __construct() {
        // Instantiate the cache class
        $this->weather_cache = new Weather_Cache();
    }

    /**
     * Public method to detect the user's browser language
     *
     * @return string The user's language code (e.g., "fa" for Persian, "en" for English).
     */
    public function get_browser_language() {
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return $language;
    }

    /**
     * Get the weather data for a given city
     *
     * @param string $city_name The name of the city to fetch weather data for.
     * @return array|bool Weather data on success, false on failure.
     */
    public function get_weather_data($city_name) {
        // Check if the data is cached
        $cached_data = $this->weather_cache->get_cached_weather_data($city_name);

        if (false !== $cached_data) {
            return $cached_data;
        }

        // Detect the user's browser language
        $language = $this->get_browser_language();

        // Determine the units parameter based on language
        $units = ($language === 'fa') ? 'metric' : 'imperial';

        // API request
        $api_key = '4503f87f2a76fb1b5c028df33323cf5c';
        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode($city_name) . '&appid=' . $api_key . '&units=' . $units;

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // If data is successfully retrieved, store it in the cache
        if (isset($data['main'])) {
            $weather_data = [
                'city'        => $data['name'],
                'temp'        => $data['main']['temp'],
                'icon'        => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
                'description' => $data['weather'][0]['description'],
            ];

            // Cache the data
            $this->weather_cache->set_weather_data_to_cache($city_name, $weather_data);

            return $weather_data;
        }

        return false;
    }
}
