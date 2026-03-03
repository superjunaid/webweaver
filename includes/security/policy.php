<?php
/**
 * Security Policies
 */

namespace WP_MCP_Connector\Security;

class Policy {
    public static function is_draft_only_mode() {
        return (bool)get_option('webweaver_draft_only', 1);
    }

    public static function is_allowed_post_type($type) {
        $allowed = get_option('webweaver_allowed_post_types', ['post', 'page']);
        return in_array($type, (array)$allowed, true);
    }

    public static function is_post_allowed($post_id) {
        // Check protected pages list.
        $protected = get_option('webweaver_protected_pages', []);

        if (in_array($post_id, (array)$protected, true)) {
            return false;
        }

        // Check allowed page tree.
        $allowed_pages = get_option('webweaver_allowed_page_ids', []);
        if (!empty($allowed_pages)) {
            return in_array($post_id, (array)$allowed_pages, true);
        }

        return true;
    }

    public static function get_rate_limit() {
        return (int)get_option('webweaver_rate_limit', 60);
    }
}
