<?php
/**
 * Check User Permissions & Capabilities
 * Run: php check-permissions.php [user_id]
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

// Get user ID from argument or default to first admin
$user_id = isset($argv[1]) ? intval($argv[1]) : 1;

wp_set_current_user($user_id);

$user = get_user_by('ID', $user_id);

if (!$user) {
    echo "❌ User not found (ID: $user_id)\n";
    echo "Usage: php check-permissions.php [user_id]\n";
    echo "\nAvailable users:\n";
    
    $users = get_users(['fields' => ['ID', 'user_login']]);
    foreach ($users as $u) {
        echo "  ID {$u->ID}: {$u->user_login}\n";
    }
    exit(1);
}

echo "=== User Permissions Check ===\n\n";
echo "User: " . $user->user_login . " (ID: $user_id)\n";
echo "Email: " . $user->user_email . "\n";
echo "Roles: " . implode(', ', $user->roles) . "\n\n";

// Check all capabilities
$capabilities = [
    'read_posts' => 'Read existing posts',
    'edit_posts' => 'Edit own posts',
    'edit_others_posts' => 'Edit any post',
    'publish_posts' => 'Publish posts',
    'create_posts' => 'Create new posts (alias for publish_posts)',
    'delete_posts' => 'Delete posts',
    'upload_files' => 'Upload media files',
    'manage_options' => 'Admin access',
];

echo "Capabilities:\n";
echo "─────────────────────────────────────────────────────\n";

$missing = [];
$found = [];

foreach ($capabilities as $cap => $description) {
    $has = current_user_can($cap);
    $status = $has ? '✅' : '❌';
    
    printf("%-25s %s %s\n", $cap, $status, $description);
    
    if (!$has) {
        $missing[] = $cap;
    } else {
        $found[] = $cap;
    }
}

echo "\n=== API Requirements ===\n\n";

$requirements = [
    'List Posts' => ['read_posts'],
    'Get Post Details' => ['read_posts'],
    'Create Post' => ['publish_posts', 'edit_posts'],
    'Update Post' => ['edit_posts'],
    'Upload Media' => ['upload_files'],
    'Set Featured Image' => ['edit_posts', 'upload_files'],
];

foreach ($requirements as $feature => $required_caps) {
    $can_do = true;
    foreach ($required_caps as $cap) {
        if (!current_user_can($cap)) {
            $can_do = false;
            break;
        }
    }
    
    $status = $can_do ? '✅' : '❌';
    $caps_str = implode(', ', $required_caps);
    printf("%s %-25s [%s]\n", $status, $feature, $caps_str);
}

echo "\n=== Solution ===\n\n";

if ($missing) {
    echo "Missing capabilities: " . implode(', ', $missing) . "\n\n";
    
    echo "🔧 FIX:\n";
    echo "Option 1 - WordPress Admin (Easiest):\n";
    echo "  1. Users > " . $user->user_login . "\n";
    echo "  2. Change role to 'Editor'\n";
    echo "  3. Click 'Update User'\n\n";
    
    echo "Option 2 - Programmatically:\n";
    echo "  Run: php check-permissions-fix.php $user_id\n\n";
    
    echo "Role Capabilities:\n";
    echo "  • Subscriber: Read only\n";
    echo "  • Contributor: Create/edit own (no publish)\n";
    echo "  • Author: Full control of own posts\n";
    echo "  • Editor: Full control of all posts ✓ RECOMMENDED\n";
    echo "  • Administrator: Everything ✓\n";
} else {
    echo "✅ User has all required permissions!\n";
}

echo "\n=== Current Role Details ===\n\n";

$role_name = $user->roles[0] ?? 'none';
$wp_roles = wp_roles();
$role = $wp_roles->get_role($role_name);

if ($role) {
    echo "Role: $role_name\n";
    echo "Display Name: " . $role->display_name . "\n";
    echo "Capabilities for this role:\n";
    
    $relevant_caps = ['read', 'edit_posts', 'publish_posts', 'upload_files'];
    $has_any = false;
    
    foreach ($relevant_caps as $cap) {
        if (isset($role->capabilities[$cap]) && $role->capabilities[$cap]) {
            echo "  ✓ $cap\n";
            $has_any = true;
        }
    }
    
    if (!$has_any) {
        echo "  (Limited permissions)\n";
    }
} else {
    echo "No role assigned!\n";
}
