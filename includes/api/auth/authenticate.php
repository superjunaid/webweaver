<?php
/**
 * Authentication
 */

namespace WP_MCP_Connector\API\Auth;

class Authenticate {
    public static function check_auth(\WP_REST_Request $request) {
        // REST API will use standard WordPress authentication.
        // Application Passwords work out of the box with WP Core.
        if (is_user_logged_in()) {
            return true;
        }

        return new \WP_Error(
            'rest_forbidden',
            'Unauthorized',
            ['status' => 401]
        );
    }

    public static function check_auth_admin(\WP_REST_Request $request) {
        if (is_user_logged_in() && current_user_can('manage_options')) {
            return true;
        }

        return new \WP_Error(
            'rest_forbidden',
            'Unauthorized: Admin only',
            ['status' => 403]
        );
    }
}
