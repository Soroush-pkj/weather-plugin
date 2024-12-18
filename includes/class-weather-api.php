<?php

class Weather_API {
    private $weather_cache;

    public function __construct() {
        
        $this->weather_cache = new Weather_Cache();
    }

    public function get_browser_language() {
        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        return $language;
    }

    
    public function get_weather_data($city_name) {
        
        $language = $this->get_browser_language();

        
        $units = ($language === 'fa') ? 'metric' : 'imperial';

        // Generate a cache key that includes the city and units
        $cache_key = $this->weather_cache->get_cache_key($city_name, $units);

        // Check if the data is cached
        $cached_data = $this->weather_cache->get_cached_weather_data($cache_key);
        if (false !== $cached_data) {
            return $cached_data;
        }

        $api_key = '4503f87f2a76fb1b5c028df33323cf5c';
        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode($city_name) . '&appid=' . $api_key . '&units=' . $units;

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // store it in the cache
        if (isset($data['main'])) {
            $weather_data = [
                'city'        => $data['name'],
                'temp'        => $data['main']['temp'],
                'icon'        => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
                'description' => $data['weather'][0]['description'],
            ];

            // Cache the data
            $this->weather_cache->set_weather_data_to_cache($cache_key, $weather_data);

            return $weather_data;
        }

        return false;
    }
}
