<?php
/**
 * Authentication
 */

namespace WP_MCP_Connector\API\Auth;

class Authenticate {
    public static function check_auth(\WP_REST_Request $request) {
        // Try to authenticate via Basic Auth header
        self::handle_basic_auth();

        // Check if user is logged in
        if (is_user_logged_in()) {
            return true;
        }

        return new \WP_Error(
            'rest_forbidden',
            'Unauthorized',
            ['status' => 401]
        );
    }

    /**
     * Handle Basic Authentication (user:password in Authorization header)
     */
    private static function handle_basic_auth() {
        // Check for Authorization header
        $auth_header = '';
        
        // Try standard Authorization header
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            // Nginx compatibility
            $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        // Parse Basic Auth
        if (!empty($auth_header) && strpos($auth_header, 'Basic ') === 0) {
            $credentials = base64_decode(substr($auth_header, 6));
            
            if (strpos($credentials, ':') !== false) {
                list($username, $password) = explode(':', $credentials, 2);
                
                // Authenticate user
                $user = wp_authenticate($username, $password);
                
                if (!is_wp_error($user)) {
                    wp_set_current_user($user->ID);
                    do_action('wp_login', $user->user_login, $user);
                }
            }
        }
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
