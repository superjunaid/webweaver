<?php
/**
 * Fix .htaccess for REST API
 * Run: php fix-htaccess.php
 */

chdir(dirname(__FILE__) . '/../../..');
require_once('wp-load.php');

echo "=== Fixing .htaccess ===\n\n";

// Step 1: Flush rewrite rules
echo "1. Flushing rewrite rules...\n";
flush_rewrite_rules(false);
echo "   ✓ Done\n\n";

// Step 2: Check .htaccess
$htaccess_file = ABSPATH . '.htaccess';
echo "2. Checking .htaccess at: $htaccess_file\n";

if (file_exists($htaccess_file)) {
    $content = file_get_contents($htaccess_file);
    echo "   File exists, current size: " . strlen($content) . " bytes\n";
    
    if (empty($content) || strpos($content, 'BEGIN WordPress') === false) {
        echo "   ⚠️  File is empty or missing WordPress directives\n";
    } else {
        echo "   ✓ File has WordPress directives\n";
    }
} else {
    echo "   ❌ File does not exist\n";
}

// Step 3: Write proper .htaccess
echo "\n3. Writing REST API rules...\n";

$htaccess_content = <<<'HTACCESS'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# Protect wp-config.php
RewriteRule ^wp-config\.php$ - [L]

# Protect .htaccess
<Files ".htaccess">
    Order Deny,Allow
    Deny from all
</Files>

# Redirect if WordPress is in subdirectory
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTACCESS;

if (is_writable(dirname($htaccess_file))) {
    file_put_contents($htaccess_file, $htaccess_content);
    echo "   ✓ .htaccess updated\n";
    echo "   Rules written: mod_rewrite configuration\n";
} else {
    echo "   ❌ Cannot write to " . dirname($htaccess_file) . "\n";
    echo "   Please manually create/update .htaccess\n";
}

// Step 4: Final flush
echo "\n4. Final rewrite flush...\n";
flush_rewrite_rules(false);
echo "   ✓ Done\n";

// Step 5: Verify
echo "\n5. Verification:\n";
echo "   Permalink structure: " . get_option('permalink_structure') . "\n";
echo "   REST API namespace: wp-mcp/v1\n";

// Check if routes are registered
do_action('rest_api_init');
$server = rest_get_server();
$routes = $server->get_routes();
$count = count(array_filter(array_keys($routes), fn($r) => strpos($r, 'wp-mcp') !== false));
echo "   WebWeaver routes: $count found\n";

echo "\n✅ Configuration complete!\n";
echo "\nNow try:\n";
echo "   curl http://localhost:8888/wp-json/wp-mcp/v1/tools\n";
