<?php
/**
 * WebWeaver Plugin Validation Script
 * Run via: php validate-plugin.php
 */

// Change to plugin directory
$plugin_dir = dirname(__FILE__);
chdir($plugin_dir);

echo "=== WebWeaver Plugin Validation ===\n";
echo "Plugin path: $plugin_dir\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Check PHP version
if (version_compare(PHP_VERSION, '7.4', '<')) {
    $errors[] = "PHP version must be 7.4+, current: " . PHP_VERSION;
} else {
    $success[] = "PHP version OK: " . PHP_VERSION;
}

// 2. Check all PHP files for syntax errors
echo "Checking PHP syntax...\n";
$pattern = defined('GLOB_RECURSIVE') ? 'includes/**/*.php' : 'includes/*/*.php';
$files = array_filter(
    array_merge(
        glob($pattern),
        glob('includes/*/*/*.php'),
        glob('includes/*.php')
    ),
    fn($f) => is_file($f)
);

foreach ($files as $file) {
    $output = [];
    $return = 0;
    exec("php -l " . escapeshellarg($file), $output, $return);
    if ($return !== 0) {
        $errors[] = "Syntax error in $file: " . implode(' ', $output);
    }
}
if (empty($errors)) {
    $success[] = "All PHP files have valid syntax";
}

// 3. Check autoloader exists
if (!file_exists('includes/autoloader.php')) {
    $errors[] = "Missing includes/autoloader.php";
} else {
    $success[] = "Autoloader found";
}

// 4. Check main plugin class exists
if (!file_exists('includes/plugin.php')) {
    $errors[] = "Missing includes/plugin.php";
} else {
    $success[] = "Main plugin class found";
}

// 5. Check critical classes/methods match
$checks = [
    'includes/api/routes.php' => ['Routes' => ['register_routes']],
    'includes/admin/menu.php' => ['Menu' => ['register_menu']],
    'includes/admin/assets.php' => ['Assets' => ['enqueue']],
    'includes/builders/registry.php' => ['Registry' => ['detect_builders']],
    'includes/logging/activity.php' => ['Activity' => ['init']],
    'includes/security/ratelimit.php' => ['RateLimit' => ['init', 'check_limit']],
    'includes/api/auth/authenticate.php' => ['Authenticate' => ['check_auth', 'check_auth_admin']],
    'includes/database/install.php' => ['Install' => ['create_tables']],
];

foreach ($checks as $file => $classes) {
    if (!file_exists($file)) {
        $errors[] = "Missing file: $file";
        continue;
    }
    
    $content = file_get_contents($file);
    
    foreach ($classes as $class => $methods) {
        if (strpos($content, "class $class") === false) {
            $errors[] = "Class $class not found in $file";
        }
        
        foreach ($methods as $method) {
            if (strpos($content, "function $method") === false) {
                $errors[] = "Method $class::$method not found in $file";
            }
        }
    }
}

if (count($errors) === 0) {
    $success[] = "All required classes and methods exist";
}

// 6. Check for required WordPress functions in hooks
$hook_checks = [
    'wp-webweaver.php' => ['add_action', 'register_activation_hook', 'register_deactivation_hook'],
];

foreach ($hook_checks as $file => $funcs) {
    $content = file_get_contents($file);
    foreach ($funcs as $func) {
        if (strpos($content, $func) === false) {
            $warnings[] = "Function $func not found in $file (may be WordPress-dependent)";
        }
    }
}

// 7. Composer.json exists
if (!file_exists('composer.json')) {
    $warnings[] = "No composer.json found";
} else {
    $success[] = "composer.json present";
}

// Output results
echo "\n";
if (!empty($success)) {
    echo "✓ SUCCESS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "  ✓ $msg\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠ WARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "  ⚠ $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "✗ ERRORS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "  ✗ $msg\n";
    }
    echo "\n";
    exit(1);
}

echo "=== Validation Complete ===\n";
echo "Plugin appears to be valid! ✓\n";
exit(0);
