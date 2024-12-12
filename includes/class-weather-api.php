<?php

class Weather_API {
    private $weather_cache;

    public function __construct() {
        // سازنده کلاس کش
        $this->weather_cache = new Weather_Cache();
    }

    // دریافت وضعیت آب و هوا برای یک شهر
    public function get_weather_data( $city_name ) {
        // اول بررسی می‌کنیم که آیا داده‌ها در کش موجود است
        $cached_data = $this->weather_cache->get_cached_weather_data( $city_name );

        if ( false !== $cached_data ) {
            return $cached_data;
        }

        // اگر داده‌ها در کش موجود نبود، از API دریافت می‌کنیم
        $api_key = '4503f87f2a76fb1b5c028df33323cf5c';
        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . urlencode( $city_name ) . '&appid=' . $api_key . '&units=metric';

        $response = wp_remote_get( $url );
        if ( is_wp_error( $response ) ) {
            return false;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        // اگر داده‌ها از API به‌درستی دریافت شد، آن‌ها را در کش ذخیره می‌کنیم
        if ( isset( $data['main'] ) ) {
            $weather_data = [
                'city'       => $data['name'],
                'temp'       => $data['main']['temp'],
                'icon'       => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
                'description'=> $data['weather'][0]['description'],
            ];

            // ذخیره داده‌ها در کش
            $this->weather_cache->set_weather_data_to_cache( $city_name, $weather_data );

            return $weather_data;
        }

        return false;
    }
}
