<?php
/*
Plugin Name: Weather Plugin
Description: A weather plugin displaying current weather and 5-day trends.
Version: 1.0
Author: Soroush Paknezhad
*/



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include necessary classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-weather-api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-weather-view.php';

// Enqueue styles
function weather_plugin_enqueue_styles() {
    wp_enqueue_style( 'weather-style', plugin_dir_url( __FILE__ ) . 'assets/css/weather-style.css' );
}
add_action( 'wp_enqueue_scripts', 'weather_plugin_enqueue_styles' );

// Register the [weather] shortcode
function weather_plugin_register_shortcode() {
    $weather_api = new Weather_API();
    $weather_view = new Weather_View( $weather_api );
    return $weather_view->get_weather_shortcode();
}
add_shortcode( 'weather', 'weather_plugin_register_shortcode' );
