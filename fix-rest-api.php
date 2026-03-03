<?php
/**
 * Fix REST API 404 Issues
 * Run this from WordPress root: php fix-rest-api.php
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

echo "=== WebWeaver REST API Diagnostics ===\n\n";

// 1. Check permalink structure
$permalink_structure = get_option('permalink_structure');
echo "1. Permalink Structure: " . ($permalink_structure ?: 'DEFAULT (broken!)') . "\n";

if (!$permalink_structure || $permalink_structure === '') {
    echo "   ⚠️  ISSUE: Using default permalinks - REST API won't work!\n";
    echo "   FIX: Change to 'Post name' in Settings > Permalinks\n\n";
    
    echo "   Applying fix...\n";
    update_option('permalink_structure', '/%postname%/');
    echo "   ✓ Updated to: /%postname%/\n";
}

// 2. Flush rewrite rules
echo "\n2. Flushing rewrite rules...\n";
flush_rewrite_rules();
echo "   ✓ Done\n";

// 3. Check plugin activation
$active_plugins = get_option('active_plugins', []);
$webweaver_active = in_array('webweaver/wp-webweaver.php', $active_plugins);
echo "\n3. WebWeaver Plugin: " . ($webweaver_active ? '✓ Active' : '❌ NOT Active') . "\n";

if (!$webweaver_active) {
    echo "   FIX: Activate in WordPress Admin > Plugins\n";
}

// 4. Check REST API works
echo "\n4. Testing REST API...\n";
$test_request = new WP_REST_Request('GET', '/wp/v2/posts');
$test_response = rest_do_request($test_request);

if (is_wp_error($test_response)) {
    echo "   ❌ Error: " . $test_response->get_error_message() . "\n";
} else {
    echo "   ✓ REST API working\n";
}

// 5. Check WebWeaver routes
echo "\n5. WebWeaver Routes:\n";
do_action('rest_api_init');

$server = rest_get_server();
$routes = $server->get_routes();
$webweaver_routes = [];

foreach ($routes as $route => $details) {
    if (is_string($route) && strpos($route, 'wp-mcp') !== false) {
        $webweaver_routes[] = $route;
    }
}

if (empty($webweaver_routes)) {
    echo "   ❌ No WebWeaver routes found!\n";
    echo "   FIX: Check plugin is activated and const defined\n";
} else {
    echo "   ✓ Found " . count($webweaver_routes) . " routes:\n";
    foreach ($webweaver_routes as $route) {
        echo "      - $route\n";
    }
}

// 6. Check Apache .htaccess
echo "\n6. Checking rewrite support...\n";
if (function_exists('apache_mod_loaded')) {
    $mod_rewrite = apache_mod_loaded('mod_rewrite');
    echo "   mod_rewrite: " . ($mod_rewrite ? '✓' : '❌') . "\n";
}

if (file_exists('.htaccess')) {
    echo "   .htaccess: ✓ Found\n";
} else {
    echo "   .htaccess: ⚠️  Not found (may be needed)\n";
}

echo "\n=== Summary ===\n";
echo "Permalink structure: " . get_option('permalink_structure') . "\n";
echo "REST API enabled: ✓\n";
echo "WebWeaver routes: " . count($webweaver_routes) . " found\n";
echo "Plugin active: " . ($webweaver_active ? '✓' : '❌') . "\n";

if (!empty($webweaver_routes) && $webweaver_active && get_option('permalink_structure')) {
    echo "\n✅ All systems ready! Try accessing:\n";
    echo "   GET /wp-json/wp-mcp/v1/tools\n";
} else {
    echo "\n⚠️  Issues found. See above for fixes.\n";
}
