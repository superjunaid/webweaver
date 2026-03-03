<?php
/**
 * Builders Registry
 */

namespace WP_MCP_Connector\Builders;

class Registry {
    private static $active = [];

    public static function detect_builders() {
        self::$active = [];

        // Elementor.
        if (class_exists('\Elementor\Plugin')) {
            self::$active['elementor'] = [
                'name' => 'Elementor',
                'version' => \Elementor\Plugin::instance()->get_version(),
                'status' => 'active',
            ];
        }

        // Divi.
        if (function_exists('et_theme_builder_post_has_content')) {
            self::$active['divi'] = [
                'name' => 'Divi',
                'status' => 'active',
            ];
        }

        // Gutenberg is always available in WP 6.x+.
        self::$active['gutenberg'] = [
            'name' => 'Gutenberg',
            'status' => 'active',
        ];
    }

    public static function get_active_builders() {
        return self::$active;
    }

    public static function is_builder_active($builder) {
        return isset(self::$active[$builder]);
    }
}
