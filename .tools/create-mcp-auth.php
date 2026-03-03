<?php
/**
 * Create MCP Authentication Keys for Manus.im or other MCP Clients
 * Run: php create-mcp-auth.php [user_id]
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

$user_id = isset($argv[1]) ? intval($argv[1]) : 1;

$user = get_user_by('ID', $user_id);

if (!$user) {
    echo "❌ User not found (ID: $user_id)\n";
    echo "Usage: php create-mcp-auth.php [user_id]\n";
    exit(1);
}

echo "=== WebWeaver MCP Authentication Setup ===\n\n";
echo "User: " . $user->user_login . " (ID: $user_id)\n";
echo "Email: " . $user->user_email . "\n\n";

// Use the Authenticate class
require_once('wp-content/plugins/webweaver/includes/api/auth/authenticate.php');

// Generate API Key
$api_key = \WP_MCP_Connector\API\Auth\Authenticate::generate_api_key($user_id);

echo "✅ API Key Generated!\n\n";
echo "🔑 API Key (save this securely):\n";
echo "   " . $api_key . "\n\n";

// Show all auth methods
echo "═══════════════════════════════════════════════════════\n";
echo "Authentication Methods for Manus.im or MCP Clients\n";
echo "═══════════════════════════════════════════════════════\n\n";

echo "METHOD 1: X-API-Key Header (Recommended)\n";
echo "────────────────────────────────────────\n";
echo "Header Name:  X-API-Key\n";
echo "Header Value: " . $api_key . "\n";
echo "Example: curl -H 'X-API-Key: " . substr($api_key, 0, 10) . "...' http://yoursite.com/wp-json/wp-mcp/v1/tools\n\n";

echo "METHOD 2: Basic Auth Header\n";
echo "────────────────────────────\n";
$basic_auth = base64_encode($user->user_login . ':' . (isset($argv[2]) ? $argv[2] : 'password'));
echo "Header Name:  Authorization\n";
echo "Header Value: Basic " . $basic_auth . "\n";
echo "Note: Use your WordPress password or app password\n\n";

echo "METHOD 3: Bearer Token\n";
echo "──────────────────────\n";
echo "Header Name:  Authorization\n";
echo "Header Value: Bearer " . $api_key . "\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "For Manus.im:\n";
echo "═══════════════════════════════════════════════════════\n";
echo "\n1. Add custom header:\n";
echo "   Header Name:  X-API-Key\n";
echo "   Header Value: " . $api_key . "\n\n";

echo "2. Or use Authorization header:\n";
echo "   Header Name:  Authorization\n";
echo "   Header Value: Basic " . base64_encode($user->user_login . ':yourpassword') . "\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "Server Configuration for Manus.im:\n";
echo "═══════════════════════════════════════════════════════\n\n";

echo "Server URL: https://yoursite.com/wp-json/wp-mcp/v1\n\n";

echo "Custom Headers:\n";
echo "  [+] Add custom header\n";
echo "  Header Name:  X-API-Key\n";
echo "  Header Value: " . $api_key . "\n\n";

echo "Then click: Try it out > Save\n\n";

echo "═══════════════════════════════════════════════════════\n";
echo "✅ Ready for Manus.im!\n";
echo "═══════════════════════════════════════════════════════\n";
