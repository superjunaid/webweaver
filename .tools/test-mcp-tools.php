<?php
/**
 * Test MCP Tools Manifest
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

do_action('rest_api_init');

wp_set_current_user(1);

$request = new WP_REST_Request('GET', '/wp-mcp/v1/tools');
$response = rest_do_request($request);

echo "=== WebWeaver MCP Tools Manifest ===\n\n";

if (is_wp_error($response)) {
    echo "Error: " . $response->get_error_message() . "\n";
    exit(1);
}

$tools = $response->get_data();

if (empty($tools)) {
    echo "No tools found\n";
    exit(1);
}

if (isset($tools['tools'])) {
    $toolsList = $tools['tools'];
    echo "Found " . count($toolsList) . " tools:\n\n";
    
    foreach ($toolsList as $tool) {
        echo "Tool: " . $tool['name'] . "\n";
        echo "  Description: " . $tool['description'] . "\n";
        echo "  Endpoint: " . $tool['endpoint'] . "\n";
        if (isset($tool['parameters'])) {
            echo "  Parameters:\n";
            foreach ($tool['parameters'] as $param => $type) {
                echo "    - $param: $type\n";
            }
        }
        echo "\n";
    }
    
    echo "Manifest info:\n";
    echo "  Version: " . $tools['version'] . "\n";
    echo "  Site: " . $tools['site_url'] . "\n";
    echo "  Active Builders: " . implode(', ', $tools['builders']) . "\n";
    echo "  Capabilities: " . json_encode($tools['capabilities']) . "\n";
    echo "  Policies: " . json_encode($tools['policies']) . "\n";
} else {
    echo "Tools format:\n";
    echo json_encode($tools, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
}
echo "\nTools are accessible via REST API at: /wp-json/wp-mcp/v1/tools\n";
echo "With authentication: Basic auth or WordPress session\n";
