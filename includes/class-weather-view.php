<?php

class Weather_View {

    private $weather_api;

    // Constructor accepting the Weather_API class
    public function __construct( Weather_API $weather_api ) {
        $this->weather_api = $weather_api;
    }

    // Shortcode to display weather for the cities
    public function get_weather_shortcode() {
        $cities = ['Tehran', 'New York', 'Paris'];
        $output = '<div class="weather-container">';

        foreach ( $cities as $city ) {
            $weather_data = $this->weather_api->get_weather_data( $city );
            
            if ( $weather_data ) {
                $output .= '<div class="weather-city">';
                $output .= '<h3>' . esc_html( $weather_data['city'] ) . '</h3>';
                $output .= '<img src="' . esc_url( $weather_data['icon'] ) . '" alt="Weather icon">';
                $output .= '<p>Temperature: ' . esc_html( $weather_data['temp'] ) . '°C</p>';
                $output .= '<p>' . esc_html( $weather_data['description'] ) . '</p>';
                $output .= '<p>updated: ' . esc_html( $weather_data['time'] ) . '</p>';

                $output .= '</div>';
            } else {
                $output .= '<p>Weather data not available for ' . esc_html( $city ) . '.</p>';
            }
        }

        $output .= '</div>';
        return $output;
    }
}
