<?php
/**
 * Rate Limiting
 */

namespace WP_MCP_Connector\Security;

class RateLimit {
    private static $cache_key = 'wp_mcp_rate_limit_';

    public static function check_limit($user_id) {
        $limit = Policy::get_rate_limit();
        $key = self::$cache_key . $user_id . '_' . date('Y-m-d-H');
        $current = (int)wp_cache_get($key);

        if ($current >= $limit) {
            return false;
        }

        wp_cache_set($key, $current + 1, '', 3600);
        return true;
    }

    public static function init() {
        // Transient cleanup handled by WordPress core.
    }
}
