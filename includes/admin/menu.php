<?php
/**
 * Admin Menu
 */

namespace WP_MCP_Connector\Admin;

class Menu {
    public static function register_menu() {
        add_menu_page(
            'WebWeaver',
            'WebWeaver',
            'manage_options',
            'webweaver',
            [self::class, 'render_dashboard'],
            'dashicons-admin-customizer',
            99
        );

        add_submenu_page(
            'webweaver',
            'Settings',
            'Settings',
            'manage_options',
            'webweaver-settings',
            [self::class, 'render_settings']
        );

        add_submenu_page(
            'webweaver',
            'Activity Log',
            'Activity Log',
            'manage_options',
            'webweaver-activity',
            [self::class, 'render_activity']
        );
    }

    public static function render_dashboard() {
        include WP_MCP_CONNECTOR_PATH . 'templates/admin/dashboard.php';
    }

    public static function render_settings() {
        include WP_MCP_CONNECTOR_PATH . 'templates/admin/settings.php';
    }

    public static function render_activity() {
        include WP_MCP_CONNECTOR_PATH . 'templates/admin/activity.php';
    }
}
