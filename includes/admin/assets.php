<?php
/**
 * Admin Assets
 */

namespace WP_MCP_Connector\Admin;

class Assets {
    public static function enqueue($hook) {
        if (strpos($hook, 'webweaver') === false) {
            return;
        }

        wp_enqueue_style(
            'webweaver-admin',
            WEBWEAVER_URL . 'assets/admin/admin.css',
            [],
            WEBWEAVER_VERSION
        );

        wp_enqueue_script(
            'webweaver-admin',
            WEBWEAVER_URL . 'assets/admin/admin.js',
            ['jquery', 'wp-api'],
            WEBWEAVER_VERSION,
            true
        );

        wp_localize_script('webweaver-admin', 'webweaverAdmin', [
            'apiUrl' => rest_url('wp-mcp/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }
}
