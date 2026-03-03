<?php
/**
 * Grant Missing Capabilities to User
 * Run: php grant-permissions.php [user_id]
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

$user_id = isset($argv[1]) ? intval($argv[1]) : 1;

$user = get_user_by('ID', $user_id);

if (!$user) {
    echo "❌ User not found (ID: $user_id)\n";
    exit(1);
}

echo "=== Granting Permissions ===\n";
echo "User: " . $user->user_login . " (ID: $user_id)\n\n";

// Get user object
$user_obj = new WP_User($user_id);

// Grant missing capabilities
$caps_to_grant = [
    'read_posts' => 'Read posts',
    'read' => 'Read (core)',
];

echo "Granting capabilities:\n";

foreach ($caps_to_grant as $cap => $desc) {
    if (!$user_obj->has_cap($cap)) {
        $user_obj->add_cap($cap);
        echo "✓ $cap ($desc)\n";
    } else {
        echo "✓ $cap ($desc) - already has\n";
    }
}

echo "\n=== Verification ===\n\n";

wp_set_current_user($user_id);

$check_caps = [
    'read_posts' => 'Read posts',
    'read' => 'Read',
    'edit_posts' => 'Edit posts',
    'create_posts' => 'Create posts',
    'publish_posts' => 'Publish posts',
    'upload_files' => 'Upload files',
];

echo "Current capabilities:\n";
foreach ($check_caps as $cap => $desc) {
    $has = current_user_can($cap);
    $status = $has ? '✅' : '❌';
    echo "$status $cap ($desc)\n";
}

echo "\n✅ Permissions updated!\n";
echo "\nNow try:\n";
echo "  GET /wp-json/wp-mcp/v1/posts\n";
echo "  GET /wp-json/wp-mcp/v1/tools\n";
