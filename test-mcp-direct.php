<?php
/**
 * Direct MCP Test - Run from WordPress context
 */

// Change to WordPress root
chdir(dirname(__FILE__) . '/../../..');

// Load WordPress
if (!function_exists('wp_load_alloptions')) {
    require_once('wp-load.php');
}

echo "=== WebWeaver MCP Direct Test ===\n\n";

// Check plugin activated
$active_plugins = get_option('active_plugins', []);
$plugin_basename = 'webweaver/wp-webweaver.php';

echo "Checking plugin status...\n";
if (in_array($plugin_basename, $active_plugins)) {
    echo "✓ Plugin is activated\n";
} else {
    echo "✗ Plugin is NOT activated\n";
    echo "Activating...\n";
    activate_plugin($plugin_basename);
    echo "✓ Plugin activated\n";
}

echo "\nChecking REST API routes...\n";

// Check if REST routes registered
$routes = rest_get_server()->get_routes();
$mcp_routes = [];
foreach ($routes as $route => $details) {
    if (is_string($route) && strpos($route, 'wp-mcp') !== false) {
        $mcp_routes[$route] = $details;
    }
}

if (!empty($mcp_routes)) {
    echo "✓ MCP routes registered:\n";
    foreach ($mcp_routes as $route => $details) {
        echo "  - $route\n";
    }
} else {
    echo "✗ No MCP routes found\n";
    echo "Available routes sample:\n";
    $sample = array_slice(array_keys($routes), 0, 10);
    foreach ($sample as $route) {
        echo "  - $route\n";
    }
}

echo "\nTesting Tools Endpoint...\n";

// Manually test the tools endpoint
do_action('rest_api_init');

// Create a mock request
$request = new WP_REST_Request('GET', '/wp-mcp/v1/tools');

// Mock authentication (logged in user)
wp_set_current_user(1); // Admin user

$response = rest_do_request($request);

if (is_wp_error($response)) {
    echo "✗ Error: " . $response->get_error_message() . "\n";
} else {
    $data = $response->get_data();
    echo "✓ Tools endpoint works\n";
    echo "Response status: " . $response->get_status() . "\n";
    if (!empty($data)) {
        echo "Tools count: " . count($data) . "\n";
        if (isset($data[0])) {
            echo "Sample tool: " . $data[0]['name'] . "\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
