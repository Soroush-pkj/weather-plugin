<?php

class Weather_Cron {

    private $transient_key_prefix = 'weather_data_';

    public function __construct() {
        // Schedule the cron job if it is not already scheduled
        if ( ! wp_next_scheduled( 'weather_cache_flush_event' ) ) {
            wp_schedule_event( time(), 'three_hours', 'weather_cache_flush_event' );
        }

        // Hook into the cron event to execute the cache flush
        add_action( 'weather_cache_flush_event', [ $this, 'flush_weather_cache' ] );

        // Add custom interval to WP Cron
        add_filter( 'cron_schedules', [ $this, 'add_three_hours_interval' ] );
    }

    // Define a custom interval for 3 hours
    public function add_three_hours_interval( $schedules ) {
        $schedules['three_hours'] = [
            'interval' => 10800, // 3 hours in seconds
            'display'  => __( 'Every 3 Hours' ),
        ];
        return $schedules;
    }

    // Flush weather cache by removing all transients with the prefix
    public function flush_weather_cache() {
        global $wpdb;

        // Query to find all transients with the prefix 'weather_data_'
        $transients = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_' . $this->transient_key_prefix . '%'
            )
        );

        // Loop through the results and delete each transient
        foreach ( $transients as $transient ) {
            $key = str_replace( '_transient_', '', $transient->option_name );
            delete_transient( $key );
        }
    }

    // Clear the scheduled event upon plugin deactivation
    public static function deactivate() {
        $timestamp = wp_next_scheduled( 'weather_cache_flush_event' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'weather_cache_flush_event' );
        }
    }
}

// Instantiate the class
new Weather_Cron();

// Hook into plugin deactivation to clear scheduled events
register_deactivation_hook( __FILE__, [ 'Weather_Cron', 'deactivate' ] );
