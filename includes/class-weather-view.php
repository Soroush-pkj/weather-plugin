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
    public function get_weather_shortcode()
    {


        // Search form for cities
        $output = '<div class="weather-container1">
                <p>You can customize the list of cities up to maximum of 5 cities<p/>
                <input type="text" id="weather-search" placeholder="Search cities...">
                <div id="search-results"></div>
                <div id="selected-cities"></div>
                <button id="submit-cities">Apply</button>
            </div>
            <div id="loading-indicator" style="display:none;">
                <p>Loading...</p>
            </div>
        <div class="weather-container">';



        $output .= '</div>';

        return $output;
    }
}
