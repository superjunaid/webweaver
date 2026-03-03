<?php
/**
 * Plugin Name: WebWeaver
 * Description: AI-powered page builder for WordPress - create & edit pages with any AI agent
 * Version: 0.2.0
 * Author: WebWeaver Team
 * License: GPL-2.0-or-later
 * Text Domain: webweaver
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

namespace WP_MCP_Connector;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define constants.
define('WEBWEAVER_VERSION', '0.2.0');
define('WEBWEAVER_PATH', plugin_dir_path(__FILE__));
define('WEBWEAVER_URL', plugin_dir_url(__FILE__));
define('WEBWEAVER_BASENAME', plugin_basename(__FILE__));
define('WP_MCP_CONNECTOR_PATH', WEBWEAVER_PATH);

// Autoloader.
require_once WEBWEAVER_PATH . 'includes/autoloader.php';

// Initialize plugin.
add_action('plugins_loaded', ['\WP_MCP_Connector\Plugin', 'instance']);

// Activation/Deactivation.
register_activation_hook(__FILE__, ['\WP_MCP_Connector\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['\WP_MCP_Connector\Plugin', 'deactivate']);
