<?php

class Weather_View
{

    private $weather_api;

    // Constructor accepting the Weather_API class
    public function __construct(Weather_API $weather_api)
    {
        $this->weather_api = $weather_api;
    }

    // Shortcode to display weather for the cities
    public function get_weather_shortcode() {
        $cities = ['Tehran', 'New York', 'Sydney']; // شهرهای پیش‌فرض
        $output = '<div class="weather-container">';
    
        foreach ( $cities as $city ) {
            $weather_data = $this->weather_api->get_weather_data( $city );
    
            if ( $weather_data ) {
                $output .= '<div class="weather-city">';
                $output .= '<h3>' . esc_html( $weather_data['city'] ) . '</h3>';
                $output .= '<img src="' . esc_url( $weather_data['icon'] ) . '" alt="Weather icon">';
                $output .= '<p>Temperature: ' . esc_html( $weather_data['temp'] ) . '°C</p>';
                $output .= '<p>' . esc_html( $weather_data['description'] ) . '</p>';
                $output .= '</div>';
            } else {
                $output .= '<p>Weather data not available for ' . esc_html( $city ) . '.</p>';
            }
        }
    
        $output .= '</div>';
    
        // فرم انتخاب شهر
        $output .= '
            <div class="weather-search-container">
                <input type="text" id="weather-search" placeholder="Search cities..." />
                <button id="weather-add-city">Add City</button>
                <ul id="selected-cities"></ul>
            </div>
            <button id="submit-cities">Submit</button>
            <div id="new-weather-cities"></div>
        ';
    
        return $output;
    }
    
}
