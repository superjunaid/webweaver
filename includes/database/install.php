<?php
/**
 * Database Installation
 */

namespace WP_MCP_Connector\Database;

class Install {
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Activity log table.
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mcp_activity_log (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            post_id BIGINT(20) UNSIGNED,
            builder_mode VARCHAR(20),
            result VARCHAR(20),
            message LONGTEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            PRIMARY KEY (id),
            KEY (timestamp),
            KEY (user_id),
            KEY (post_id),
            KEY (action)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
