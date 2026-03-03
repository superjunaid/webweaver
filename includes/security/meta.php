<?php
/**
 * Meta Key Allowlist
 */

namespace WP_MCP_Connector\Security;

class Meta {
    private static $allowed_keys = [
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_focuskw',
        '_rank_math_title',
        '_rank_math_description',
        'custom_seo_title',
        'custom_seo_description',
    ];

    public static function is_allowed_meta_key($key) {
        // Prevent underscore-prefixed meta unless explicitly allowed.
        if (strpos($key, '_') === 0) {
            return in_array($key, self::$allowed_keys, true);
        }
        return true;
    }

    public static function add_allowed_key($key) {
        self::$allowed_keys[] = $key;
    }
}
