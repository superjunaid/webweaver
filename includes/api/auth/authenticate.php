<?php
/**
 * Authentication - Support Basic Auth, API Keys, and MCP Clients
 */

namespace WP_MCP_Connector\API\Auth;

class Authenticate {
    // API Key option name
    const API_KEY_OPTION = 'webweaver_api_keys';

    public static function check_auth(\WP_REST_Request $request) {
        // Try multiple auth methods
        if (self::handle_bearer_token()) {
            return true;
        }
        
        if (self::handle_api_key()) {
            return true;
        }

        if (self::handle_basic_auth()) {
            return true;
        }

        // Check if user is logged in (session/cookie auth)
        if (is_user_logged_in()) {
            return true;
        }

        return new \WP_Error(
            'rest_forbidden',
            'Unauthorized. Use: Basic Auth, Bearer Token, or X-API-Key header',
            ['status' => 401]
        );
    }

    /**
     * Handle Bearer Token (OAuth-style)
     * Authorization: Bearer {token}
     */
    private static function handle_bearer_token() {
        $auth_header = self::get_auth_header();
        
        if (empty($auth_header) || strpos($auth_header, 'Bearer ') !== 0) {
            return false;
        }

        $token = substr($auth_header, 7);
        $user = self::verify_bearer_token($token);
        
        if ($user) {
            wp_set_current_user($user->ID);
            return true;
        }

        return false;
    }

    /**
     * Handle API Key authentication
     * X-API-Key: {api_key}
     */
    private static function handle_api_key() {
        $api_key = isset($_SERVER['HTTP_X_API_KEY']) ? sanitize_text_field($_SERVER['HTTP_X_API_KEY']) : '';
        
        if (empty($api_key)) {
            return false;
        }

        $user = self::verify_api_key($api_key);
        
        if ($user) {
            wp_set_current_user($user->ID);
            return true;
        }

        return false;
    }

    /**
     * Handle Basic Authentication
     * Authorization: Basic base64(username:password)
     */
    private static function handle_basic_auth() {
        $auth_header = self::get_auth_header();
        
        if (empty($auth_header) || strpos($auth_header, 'Basic ') !== 0) {
            return false;
        }

        $credentials = base64_decode(substr($auth_header, 6));
        
        if (strpos($credentials, ':') === false) {
            return false;
        }

        list($username, $password) = explode(':', $credentials, 2);
        
        // Try to authenticate user
        $user = wp_authenticate($username, $password);
        
        if (!is_wp_error($user)) {
            wp_set_current_user($user->ID);
            do_action('wp_login', $user->user_login, $user);
            return true;
        }

        return false;
    }

    /**
     * Get Authorization header from various sources
     */
    private static function get_auth_header() {
        // Standard Authorization header
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        // Nginx compatibility
        if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        return '';
    }

    /**
     * Verify Bearer Token
     */
    private static function verify_bearer_token($token) {
        // For now, use basic validation
        // In production, implement proper token validation
        $keys = get_option(self::API_KEY_OPTION, []);
        
        if (isset($keys[$token])) {
            $user_id = $keys[$token];
            return get_user_by('ID', $user_id);
        }

        return null;
    }

    /**
     * Verify API Key
     */
    private static function verify_api_key($api_key) {
        $keys = get_option(self::API_KEY_OPTION, []);
        
        if (isset($keys[$api_key])) {
            $user_id = $keys[$api_key];
            return get_user_by('ID', $user_id);
        }

        return null;
    }

    /**
     * Generate API Key for user
     */
    public static function generate_api_key($user_id) {
        $api_key = 'wpmc_' . bin2hex(random_bytes(32));
        
        $keys = get_option(self::API_KEY_OPTION, []);
        $keys[$api_key] = $user_id;
        update_option(self::API_KEY_OPTION, $keys);
        
        return $api_key;
    }

    /**
     * Revoke API Key
     */
    public static function revoke_api_key($api_key) {
        $keys = get_option(self::API_KEY_OPTION, []);
        unset($keys[$api_key]);
        update_option(self::API_KEY_OPTION, $keys);
        return true;
    }

    /**
     * Get user's API keys
     */
    public static function get_user_api_keys($user_id) {
        $keys = get_option(self::API_KEY_OPTION, []);
        $user_keys = [];
        
        foreach ($keys as $key => $uid) {
            if ($uid == $user_id) {
                $user_keys[] = $key;
            }
        }
        
        return $user_keys;
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
