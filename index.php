<?php
/*
Plugin Name: Weather Plugin
Description: A weather plugin displaying current weather and 5-day trends.
Version: 1.0
Author: Soroush Paknezhad
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-weather-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-weather-chart.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-weather-cache.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-weather-view.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-weather-cron.php';

// Initialize the plugin
function weather_plugin_init() {
    $weather_api = new Weather_API();
    $weather_view = new Weather_View($weather_api);

    // Register AJAX handlers
    $weather_view->register_ajax_handlers();
}
add_action('plugins_loaded', 'weather_plugin_init');
