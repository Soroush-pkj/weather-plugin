<?php

class Weather_API {
    private $weather_cache;
    private static $api_key = '4503f87f2a76fb1b5c028df33323cf5c'; 

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

        $cache_key = $this->weather_cache->get_cache_key($city_name, $units);

        // Check if the data is cached
        $cached_data = $this->weather_cache->get_cached_weather_data($cache_key);
        if (false !== $cached_data) {
            return $cached_data;
        }

        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode($city_name) . '&appid=' . self::$api_key . '&units=' . $units;

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        // Store it in the cache
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

    public function get_5days_temp($city_name) {
        $language = $this->get_browser_language();
        $units = ($language === 'fa') ? 'metric' : 'imperial';

        $cache_key = $this->weather_cache->get_cache_key($city_name . '_5days', $units);

        // Check the cache
        $cached_data = $this->weather_cache->get_cached_weather_data($cache_key);
        if (false !== $cached_data) {
            return $cached_data;
        }

        $url = 'https://api.openweathermap.org/data/2.5/forecast?q=' . urlencode($city_name) . '&appid=' . self::$api_key . '&units=' . $units;

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['list']) || count($data['list']) < 1) {
            return false;
        }

        
        $daily_temps = [];
        foreach ($data['list'] as $entry) {
            $date = explode(' ', $entry['dt_txt'])[0];
            $time = explode(' ', $entry['dt_txt'])[1];

            if ($time === '12:00:00' && !isset($daily_temps[$date])) {
                $daily_temps[$date] = [
                    'date' => $date,
                    'temp' => $entry['main']['temp'],
                ];
            }
        }

        if (count($daily_temps) < 5) {
            return false;
        }

        $temps_with_dates = array_values(array_slice($daily_temps, 0, 5));

        $result = [
            'city'  => $data['city']['name'],
            'temps' => $temps_with_dates,
        ];

        $this->weather_cache->set_weather_data_to_cache($cache_key, $result);

        return $result;
    }
}
