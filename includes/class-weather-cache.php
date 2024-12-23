<?php

class Weather_Cache {
    private $transient_key_prefix = 'weather_data_';

    
    public function get_cache_key($city_name, $units) {
        return $this->transient_key_prefix . sanitize_title($city_name) . '_' . $units;
    }

    
    public function get_cached_weather_data($cache_key) {
        return get_transient($cache_key);
    }

    
    public function set_weather_data_to_cache($cache_key, $weather_data) {
        // Store data in cache for 15 minutes (900 seconds)
        set_transient($cache_key, $weather_data, 900);
    }
}
