<?php

class Weather_Cache {
    // کلید کش
    private $transient_key_prefix = 'weather_data_';

    // دریافت داده‌های کش شده
    public function get_cached_weather_data( $city_name ) {
        // بررسی کش
        $transient_key = $this->transient_key_prefix . sanitize_title( $city_name );
        $cached_data = get_transient( $transient_key );

        // اگر داده کش شده وجود داشت، آن را برمی‌گردانیم
        if ( false !== $cached_data ) {
            return $cached_data;
        }

        // اگر کش وجود نداشت، داده‌ها را از API بارگذاری می‌کنیم
        return false;
    }

    // ذخیره داده‌ها در کش
    public function set_weather_data_to_cache( $city_name, $weather_data ) {
        $transient_key = $this->transient_key_prefix . sanitize_title( $city_name );

        // ذخیره داده‌ها در کش برای مدت 15 دقیقه (900 ثانیه)
        set_transient( $transient_key, $weather_data, 900 );
    }
}
