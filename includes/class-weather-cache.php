<?php

class Weather_Cache {
    private $transient_key_prefix = 'weather_data_';

    /**
     * Generate a cache key based on city and units
     */
    public function get_cache_key($city_name, $units) {
        return $this->transient_key_prefix . sanitize_title($city_name) . '_' . $units;
    }

    /**
     * Get cached weather data
     */
    public function get_cached_weather_data($cache_key) {
        return get_transient($cache_key);
    }

    /**
     * Store weather data in cache
     */
    public function set_weather_data_to_cache($cache_key, $weather_data) {
        // Store data in cache for 15 minutes (900 seconds)
        set_transient($cache_key, $weather_data, 900);
    }
}
