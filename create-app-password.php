<?php
/**
 * Create Application Password for API Access
 * Run: php create-app-password.php [user_id] [app_name]
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

$user_id = isset($argv[1]) ? intval($argv[1]) : 1;
$app_name = isset($argv[2]) ? $argv[2] : 'WebWeaver MCP';

$user = get_user_by('ID', $user_id);

if (!$user) {
    echo "❌ User not found (ID: $user_id)\n";
    exit(1);
}

echo "=== Creating Application Password ===\n\n";
echo "User: " . $user->user_login . " (ID: $user_id)\n";
echo "App Name: $app_name\n\n";

// Create application password
if (function_exists('WP_Application_Passwords::create_new_application_password')) {
    $result = WP_Application_Passwords::create_new_application_password(
        $user_id,
        $app_name
    );
    
    if (is_wp_error($result)) {
        echo "❌ Error: " . $result->get_error_message() . "\n";
        exit(1);
    }
    
    list($password, $app) = $result;
    
    echo "✅ Application Password Created!\n\n";
    echo "Application Name: " . $app['name'] . "\n";
    echo "UUID: " . $app['uuid'] . "\n\n";
    
    echo "🔑 PASSWORD (save this somewhere safe!):\n";
    echo "   " . $password . "\n\n";
    
    echo "📝 How to use:\n";
    echo "   Username: " . $user->user_login . "\n";
    echo "   Password: " . substr($password, 0, 4) . " " . substr($password, 5, 4) . " " . substr($password, 10, 4) . " " . substr($password, 15, 4) . "\n\n";
    
    echo "🔗 Create Basic Auth Header:\n";
    $creds = base64_encode($user->user_login . ':' . $password);
    echo "   Authorization: Basic " . $creds . "\n\n";
    
    echo "💾 Test it:\n";
    echo "   curl -H 'Authorization: Basic " . substr($creds, 0, 20) . "...' \\\n";
    echo "     http://localhost:8888/wp-json/wp-mcp/v1/tools\n\n";
    
} else {
    echo "❌ Application Passwords not available\n";
    echo "WordPress 5.6+ required\n";
    exit(1);
}
