<?php

class Weather_Chart {


    public function __construct() {
        add_shortcode('weather-chart', [ $this, 'render_weather_chart' ]);
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_chart_assets' ]);
    }

    
    public function enqueue_chart_assets() {
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }

    // Render the weather chart shortcode
    public function render_weather_chart() {
        ob_start();
        ?>
        <div id="weather-chart-container">
            <canvas id="weather-chart" ></canvas>
        </div>
        <?php
        return ob_get_clean();
    }
}


new Weather_Chart();
