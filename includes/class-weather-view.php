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
        // لیست پیش‌فرض شهرها
        $default_cities = ['Tehran', 'New York', 'Sydney'];
        $cities = [];

        // اگر شهری از طرف کاربر ارسال شده باشد
        if (isset($_POST['selected_cities']) && ! empty($_POST['selected_cities'])) {
            $cities = json_decode(sanitize_text_field(wp_unslash($_POST['selected_cities'])), true);
        }

        // اگر هیچ شهری از طرف کاربر انتخاب نشده باشد، از شهرهای پیش‌فرض استفاده می‌کنیم
        if (empty($cities)) {
            $cities = $default_cities;
        }
        // فرم جستجوی شهر
       
        $output = '<div class="weather-search-container">
                <input type="text" id="weather-search" placeholder="Search cities...">
                <div id="search-results"></div>
                <div id="selected-cities"></div>
                <button id="submit-cities">Apply</button>
            </div>
            <div id="loading-indicator" style="display:none;">
    <p>Loading...</p>
</div>
        <div class="weather-container">';

        // دریافت اطلاعات وضعیت آب و هوا برای هر شهر
        foreach ($cities as $city) {
            $weather_data = $this->weather_api->get_weather_data($city);

            if ($weather_data) {
                $output .= '<div class="weather-city">';
                $output .= '<h3>' . esc_html($weather_data['city']) . '</h3>';
                $output .= '<img src="' . esc_url($weather_data['icon']) . '" alt="Weather icon">';
                $output .= '<p>Temperature: ' . esc_html($weather_data['temp']) . '°C</p>';
                $output .= '<p>' . esc_html($weather_data['description']) . '</p>';

                $output .= '</div>';
            }
        }

        $output .= '</div>';



        return $output;
    }
}
