<?php

class Weather_API {

    private $api_key = '4503f87f2a76fb1b5c028df33323cf5c';
    private $base_url = 'https://api.openweathermap.org/data/2.5/weather';

    // Fetch weather data for a city
    public function get_weather_data( $city ) {
        $url = $this->base_url . '?q=' . urlencode( $city ) . '&appid=' . $this->api_key . '&units=metric&lang=en';
        
        $response = wp_remote_get( $url );
        
        if ( is_wp_error( $response ) ) {
            return null;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['main'] ) ) {
            return [
                'city'      => $data['name'],
                'temp'      => $data['main']['temp'],
                'icon'      => 'https://openweathermap.org/img/wn/' . $data['weather'][0]['icon'] . '@2x.png',
                'description' => $data['weather'][0]['description'],
            ];
        }

        return null;
    }
}
