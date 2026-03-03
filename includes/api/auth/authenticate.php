<?php
/**
 * Authentication - Support Basic Auth, API Keys, and MCP Clients
 */

namespace WP_MCP_Connector\API\Auth;

class Authenticate {
    // API Key option name
    const API_KEY_OPTION = 'webweaver_api_keys';

    /**
     * Authenticate REST requests early in the request cycle
     * Called via init hook with priority 1 (very early)
     */
    public static function authenticate_rest_request() {
        // Only process REST API requests for our namespace
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (strpos($request_uri, '/wp-json/wp-mcp/v1/') === false) {
            return;
        }

        // If user is already logged in, don't override
        if (is_user_logged_in()) {
            return;
        }

        // Try our custom auth methods
        if (self::handle_bearer_token()) {
            return;
        }
        
        if (self::handle_api_key()) {
            return;
        }

        if (self::handle_basic_auth()) {
            return;
        }
    }

    /**
     * Handle custom authentication for REST API
     * Called via rest_authentication_errors filter
     */
    public static function handle_custom_auth($auth_error) {
        // If user is already authenticated via cookies/sessions, allow it
        if (is_user_logged_in()) {
            return $auth_error;
        }

        // Try our custom auth methods
        if (self::handle_bearer_token()) {
            return true;
        }
        
        if (self::handle_api_key()) {
            return true;
        }

        if (self::handle_basic_auth()) {
            return true;
        }

        // If auth was attempted but failed, return error
        $auth_header = self::get_auth_header();
        if (!empty($auth_header)) {
            return new \WP_Error(
                'rest_forbidden',
                'Authentication failed. Invalid credentials.',
                ['status' => 401]
            );
        }

        // Return original auth error if no custom auth attempted
        return $auth_error;
    }

    public static function check_auth(\WP_REST_Request $request = null) {
        // Try multiple auth methods (order: Bearer > API Key > Basic Auth > Session)
        
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
        $username = sanitize_user($username);
        
        // Get user by login
        $user = get_user_by('login', $username);
        
        if (!$user) {
            return false;
        }
        
        // Verify password
        if (!wp_check_password($password, $user->user_pass, $user->ID)) {
            return false;
        }
        
        wp_set_current_user($user->ID);
        return true;
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
