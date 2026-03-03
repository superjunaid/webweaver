<?php
/**
 * Autoloader for WebWeaver
 */

namespace WP_MCP_Connector;

spl_autoload_register(function ($class) {
    if (strpos($class, 'WP_MCP_Connector\\') !== 0) {
        return;
    }

    $path = str_replace('WP_MCP_Connector\\', '', $class);
    $parts = explode('\\', $path);
    $parts = array_map('strtolower', $parts);
    $file = WEBWEAVER_PATH . 'includes/' . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
