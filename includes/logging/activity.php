<?php
/**
 * Activity Logging
 */

namespace WP_MCP_Connector\Logging;

class Activity {
    public static function log($post_id, $action, $result = 'success', $builder_mode = '') {
        global $wpdb;

        $user_id = get_current_user_id();
        $ip = self::get_ip();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $wpdb->insert(
            $wpdb->prefix . 'mcp_activity_log',
            [
                'user_id' => $user_id,
                'action' => $action,
                'post_id' => $post_id,
                'builder_mode' => $builder_mode,
                'result' => $result,
                'ip_address' => $ip,
                'user_agent' => $user_agent,
                'timestamp' => current_time('mysql'),
            ],
            ['%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s']
        );

        // Hook for extensions.
        do_action('wp_mcp_activity_logged', $post_id, $action, $result);
    }

    public static function init() {
        // Initialize logging subsystem.
    }

    private static function get_ip() {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }
}
