<?php
/**
 * Test Authentication Methods
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

echo "=== Testing Authentication Methods ===\n\n";

// Test 1: Cookie-based (logged in session)
echo "1. Cookie-based Auth (WordPress Session):\n";
wp_set_current_user(1);
$request = new WP_REST_Request('GET', '/wp-mcp/v1/tools');
$response = rest_do_request($request);

if (!is_wp_error($response)) {
    echo "   ✅ Works with logged-in session\n";
    echo "   Status: " . $response->get_status() . "\n";
} else {
    echo "   ❌ Error: " . $response->get_error_message() . "\n";
}

// Test 2: Basic Auth
echo "\n2. Basic Auth (user:password):\n";

// Simulate Basic Auth header
$_SERVER['PHP_AUTH_USER'] = 'admin';
$_SERVER['PHP_AUTH_PW'] = 'wordpress';

// Clear current user
wp_set_current_user(0);

// Check if basic auth is processed by WordPress
$request = new WP_REST_Request('GET', '/wp-mcp/v1/tools');
$request->set_header('Authorization', 'Basic ' . base64_encode('admin:wordpress'));

// Need to process auth
do_action('rest_api_init');

// WordPress doesn't process Basic Auth by default
// You need the "Rest API Authentication" or similar plugin

echo "   ⚠️  Basic Auth requires plugin support\n";
echo "   WordPress doesn't enable it by default\n";

// Test 3: Check what auth is enabled
echo "\n3. Checking available auth methods:\n";

$server = rest_get_server();
echo "   REST API initialized: ✓\n";

// Check if REST API is available
$route_response = rest_do_request(new WP_REST_Request('GET', '/'));
if (!is_wp_error($route_response)) {
    echo "   API accessible: ✓\n";
} else {
    echo "   API error: " . $route_response->get_error_message() . "\n";
}

echo "\n=== Solution ===\n\n";
echo "For MCP connections, use one of:\n\n";

echo "Option A - Browser/Authenticated Client:\n";
echo "  Login to WordPress first, then MCP client inherits session\n\n";

echo "Option B - Application Password Plugin:\n";
echo "  1. Install plugin: application-passwords\n";
echo "  2. Go to User > Application Passwords\n";
echo "  3. Create password\n";
echo "  4. Use in API requests\n\n";

echo "Option C - Custom Auth (for development):\n";
echo "  Modify plugin to accept custom header\n";
echo "  Or use nonce-based authentication\n\n";

echo "For Claude/MCP clients:\n";
echo "  MCP clients typically use authenticated sessions\n";
echo "  Or you need to configure API authentication in your setup\n";
