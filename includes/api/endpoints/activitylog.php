<?php
/**
 * Activity Log Endpoint
 */

namespace WP_MCP_Connector\API\Endpoints;

class ActivityLog {
    public static function get_log(\WP_REST_Request $request) {
        global $wpdb;

        $user_id = $request->get_param('user_id');
        $post_id = $request->get_param('post_id');
        $action = $request->get_param('action');
        $per_page = (int)($request->get_param('per_page') ?? 20);
        $page = (int)($request->get_param('page') ?? 1);

        $where = [];
        $params = [];

        if ($user_id) {
            $where[] = 'user_id = %d';
            $params[] = $user_id;
        }
        if ($post_id) {
            $where[] = 'post_id = %d';
            $params[] = $post_id;
        }
        if ($action) {
            $where[] = 'action = %s';
            $params[] = $action;
        }

        $where_clause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $per_page;

        $query = $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mcp_activity_log $where_clause ORDER BY timestamp DESC LIMIT %d OFFSET %d",
            array_merge($params, [$per_page, $offset])
        );

        $logs = $wpdb->get_results($query);

        // Get total count.
        $total_query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}mcp_activity_log $where_clause",
            $params
        );
        $total = (int)$wpdb->get_var($total_query);

        return rest_ensure_response([
            'data' => $logs,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
        ]);
    }
}
