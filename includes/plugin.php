<?php
/**
 * Main Plugin Class
 */

namespace WP_MCP_Connector;

class Plugin {
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->setup_hooks();
    }

    private function setup_hooks() {
        // Register REST routes.
        add_action('rest_api_init', [API\Routes::class, 'register_routes']);

        // Register admin pages.
        add_action('admin_menu', [Admin\Menu::class, 'register_menu']);
        add_action('admin_menu', [Admin\MCPConnection::class, 'register_menu']);
        add_action('admin_enqueue_scripts', [Admin\Assets::class, 'enqueue']);

        // Initialize builders.
        add_action('init', [Builders\Registry::class, 'detect_builders']);

        // Database/logging.
        add_action('init', [Logging\Activity::class, 'init']);

        // Security.
        add_action('init', [Security\RateLimit::class, 'init']);
    }

    public static function activate() {
        Database\Install::create_tables();
        update_option('webweaver_version', WEBWEAVER_VERSION);
        update_option('webweaver_draft_only', 1);
        update_option('webweaver_rate_limit', 60);
    }

    public static function deactivate() {
        // Cleanup if needed.
    }
}
