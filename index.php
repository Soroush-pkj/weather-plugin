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
if ( ! class_exists( 'Weather_API' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-weather-api.php';
}
if ( ! class_exists( 'Weather_Cache' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-weather-cache.php';
}


if ( ! class_exists( 'Weather_View' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-weather-view.php';
}

// Enqueue styles and scripts
function weather_plugin_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style( 'weather-style', plugin_dir_url( __FILE__ ) . 'assets/css/weather-style.css' );

    // Enqueue JS
    wp_enqueue_script( 'weather-search', plugin_dir_url( __FILE__ ) . 'assets/js/weather-search.js', [ 'jquery' ], null, true );

    // Localize script for AJAX URL
    wp_localize_script( 'weather-search', 'weatherSearch', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'weather_plugin_enqueue_assets' );

// Register the [weather] shortcode
function weather_plugin_register_shortcode() {
    $weather_api = new Weather_API(); // Initialize Weather API
    $weather_view = new Weather_View( $weather_api ); // Initialize Weather View with API
    return $weather_view->get_weather_shortcode();
}
add_shortcode( 'weather', 'weather_plugin_register_shortcode' );

// Add AJAX handler for selected cities
function weather_update_cities() {
    // دریافت شهرهای انتخابی از درخواست POST
    $selected_cities = isset( $_POST['selected_cities'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['selected_cities'] ) ), true ) : [];
    
    // اگر هیچ شهری انتخاب نشده باشد، از شهرهای پیش‌فرض استفاده می‌کنیم
    if ( empty( $selected_cities ) ) {
        $selected_cities = ['Tehran', 'New York', 'Sydney'];
    }

    $weather_api = new Weather_API();
    $new_weather_data = [];

    foreach ( $selected_cities as $city ) {
        $weather_data = $weather_api->get_weather_data( $city );
        if ( $weather_data ) {
            $new_weather_data[] = $weather_data;
        }
    }

    if ( ! empty( $new_weather_data ) ) {
        wp_send_json_success( $new_weather_data );
    } else {
        wp_send_json_error( 'No weather data found for selected cities.' );
    }
}


add_action( 'wp_ajax_weather_update_cities', 'weather_update_cities' );
add_action( 'wp_ajax_nopriv_weather_update_cities', 'weather_update_cities' );


