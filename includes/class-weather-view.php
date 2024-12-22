<?php

class Weather_View {
    private $weather_api;

    public function __construct(Weather_API $weather_api) {
        $this->weather_api = $weather_api;

        // ثبت اکشن برای enqueue assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('weather', [$this, 'get_weather_shortcode']);
    }

    public function enqueue_assets() {
        // اصلاح مسیر فایل‌ها
        wp_enqueue_style('weather-style', plugin_dir_url(dirname(__FILE__)) . 'assets/css/weather-style.css');
        wp_enqueue_script('weather-search', plugin_dir_url(dirname(__FILE__)) . 'assets/js/weather-search.js', ['jquery'], null, true);

        // Localize script for AJAX URL
        wp_localize_script('weather-search', 'weatherSearch', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }


    public function get_weather_shortcode() {
        $output = '<div class="weather-container1">
                <p>You can customize the list of cities up to a maximum of 5 cities<p/>
                <div class="input-container"><input type="text" id="weather-search" placeholder="Search cities..."><button id="submit-cities">Apply</button></div>
                <div id="search-results"></div>
                <div id="selected-cities"></div>
            </div>
            <div id="loading-indicator" style="display:none;">
                <p>Loading...</p>
            </div>
        <div class="weather-container">';
        $output .= '</div>';
        return $output;
    }

    public function register_ajax_handlers() {
        add_action('wp_ajax_weather_update_cities', [$this, 'update_cities']);
        add_action('wp_ajax_nopriv_weather_update_cities', [$this, 'update_cities']);
    }

    public function update_cities() {
        $selected_cities = isset($_POST['selected_cities']) ? json_decode(sanitize_text_field(wp_unslash($_POST['selected_cities'])), true) : [];

        $new_weather_data = [];
        foreach ($selected_cities as $city) {
            $weather_data = $this->weather_api->get_weather_data($city);
            if ($weather_data) {
                $new_weather_data[] = $weather_data;
            }
        }

        if (!empty($new_weather_data)) {
            wp_send_json_success($new_weather_data);
        } else {
            wp_send_json_error('No weather data found for selected cities.');
        }
    }
}
