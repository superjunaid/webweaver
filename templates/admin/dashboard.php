<?php
/**
 * WebWeaver Dashboard Template
 */

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized'));
}

use WP_MCP_Connector\Builders;
use WP_MCP_Connector\Security;

// Get system info
$version = WEBWEAVER_VERSION;
$site_url = get_site_url();
$api_url = rest_url('wp-mcp/v1');
$is_ssl = is_ssl();
$draft_only = get_option('webweaver_draft_only', 1);
$rate_limit = get_option('webweaver_rate_limit', 60);
$active_builders = Builders\Registry::get_active_builders();
$user_cap = [
    'can_read' => current_user_can('read_posts'),
    'can_edit' => current_user_can('edit_posts'),
    'can_create' => current_user_can('create_posts'),
    'can_publish' => current_user_can('publish_posts'),
    'can_upload' => current_user_can('upload_files'),
];
?>

<div class="wrap webweaver-dashboard">
    <h1><?php esc_html_e('🎨 WebWeaver', 'webweaver'); ?></h1>
    <p class="subtitle">AI-powered page builder for WordPress</p>

    <!-- Status Overview -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('System Status', 'webweaver'); ?></h2>
        <div class="webweaver-grid">
            <div class="webweaver-card">
                <div class="card-header">
                    <h3>🔧 Configuration</h3>
                </div>
                <ul class="status-list">
                    <li>
                        <span class="label">Plugin Version:</span>
                        <span class="value"><?php echo esc_html($version); ?></span>
                    </li>
                    <li>
                        <span class="label">Site URL:</span>
                        <span class="value"><code><?php echo esc_html($site_url); ?></code></span>
                    </li>
                    <li>
                        <span class="label">API Endpoint:</span>
                        <span class="value"><code><?php echo esc_html($api_url); ?></code></span>
                    </li>
                    <li>
                        <span class="label">HTTPS:</span>
                        <span class="value status-<?php echo $is_ssl ? 'success' : 'warning'; ?>">
                            <?php echo $is_ssl ? '✅ Enabled' : '⚠️ Disabled (recommended for production)'; ?>
                        </span>
                    </li>
                    <li>
                        <span class="label">Draft-Only Mode:</span>
                        <span class="value status-<?php echo $draft_only ? 'info' : 'warning'; ?>">
                            <?php echo $draft_only ? '✅ Enabled' : '⚠️ Disabled'; ?>
                        </span>
                    </li>
                    <li>
                        <span class="label">Rate Limit:</span>
                        <span class="value"><?php echo esc_html($rate_limit); ?>/hour</span>
                    </li>
                </ul>
            </div>

            <div class="webweaver-card">
                <div class="card-header">
                    <h3>🏗️ Supported Builders</h3>
                </div>
                <ul class="status-list">
                    <?php if (!empty($active_builders)) : ?>
                        <?php foreach ($active_builders as $builder => $info) : ?>
                            <li>
                                <span class="label"><?php echo esc_html($info['name']); ?>:</span>
                                <span class="value status-success">
                                    ✅ Active
                                    <?php if (!empty($info['version'])) {
                                        echo '(v' . esc_html($info['version']) . ')';
                                    } ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li>
                            <span class="value status-warning">⚠️ No builders detected</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="webweaver-card">
                <div class="card-header">
                    <h3>👤 Your Capabilities</h3>
                </div>
                <ul class="status-list">
                    <li>
                        <span class="label">Read Posts:</span>
                        <span class="value"><?php echo $user_cap['can_read'] ? '✅' : '❌'; ?></span>
                    </li>
                    <li>
                        <span class="label">Edit Posts:</span>
                        <span class="value"><?php echo $user_cap['can_edit'] ? '✅' : '❌'; ?></span>
                    </li>
                    <li>
                        <span class="label">Create Posts:</span>
                        <span class="value"><?php echo $user_cap['can_create'] ? '✅' : '❌'; ?></span>
                    </li>
                    <li>
                        <span class="label">Publish Posts:</span>
                        <span class="value"><?php echo $user_cap['can_publish'] ? '✅' : '❌'; ?></span>
                    </li>
                    <li>
                        <span class="label">Upload Files:</span>
                        <span class="value"><?php echo $user_cap['can_upload'] ? '✅' : '❌'; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('Quick Links', 'webweaver'); ?></h2>
        <div class="quick-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-settings')); ?>" class="button button-primary">
                ⚙️ Settings
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-activity')); ?>" class="button">
                📋 Activity Log
            </a>
            <a href="<?php echo esc_url($api_url . '/tools'); ?>" class="button" target="_blank">
                📡 View Tools Manifest
            </a>
            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="button button-secondary">
                ➕ Create New Post
            </a>
        </div>
    </div>

    <!-- Getting Started -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('🚀 Getting Started', 'webweaver'); ?></h2>
        <div class="webweaver-card">
            <h3>Setup Instructions for AI Agents</h3>
            <ol class="setup-steps">
                <li>
                    <strong>1. Generate Application Password</strong>
                    <p>Go to <a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . get_current_user_id())); ?>#application-passwords-section">User Settings</a> and create an Application Password for WebWeaver.</p>
                </li>
                <li>
                    <strong>2. Configure Your MCP Client</strong>
                    <p>Use these credentials to authenticate with the API:</p>
                    <div class="code-block">
Base URL: <code><?php echo esc_html($api_url); ?></code><br>
Authentication: Basic Auth or Application Password<br>
Headers: <code>Authorization: Basic base64(username:password)</code>
                    </div>
                </li>
                <li>
                    <strong>3. Fetch Tools Manifest</strong>
                    <p>Make a GET request to retrieve available tools:</p>
                    <div class="code-block">
<code>GET <?php echo esc_html($api_url); ?>/tools</code>
                    </div>
                </li>
                <li>
                    <strong>4. Start Building</strong>
                    <p>Use the available tools to:</p>
                    <ul>
                        <li>List existing posts: <code>GET /posts</code></li>
                        <li>Get post details: <code>GET /post/{id}</code></li>
                        <li>Create new posts: <code>POST /post</code></li>
                        <li>Update posts: <code>PUT /post/{id}</code></li>
                        <li>Upload media: <code>POST /media</code></li>
                        <li>Set featured images: <code>PUT /post/{id}/featured-image</code></li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>

    <!-- API Reference -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('📚 API Reference', 'webweaver'); ?></h2>
        <table class="api-reference">
            <thead>
                <tr>
                    <th>Tool</th>
                    <th>Method</th>
                    <th>Endpoint</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>list_posts</strong></td>
                    <td><span class="method-get">GET</span></td>
                    <td><code>/posts</code></td>
                    <td>List posts with filtering options (type, status, builder)</td>
                </tr>
                <tr>
                    <td><strong>get_post</strong></td>
                    <td><span class="method-get">GET</span></td>
                    <td><code>/post/{id}</code></td>
                    <td>Get full post content and metadata</td>
                </tr>
                <tr>
                    <td><strong>create_post</strong></td>
                    <td><span class="method-post">POST</span></td>
                    <td><code>/post</code></td>
                    <td>Create new post with builder content</td>
                </tr>
                <tr>
                    <td><strong>update_post</strong></td>
                    <td><span class="method-put">PUT</span></td>
                    <td><code>/post/{id}</code></td>
                    <td>Update existing post content</td>
                </tr>
                <tr>
                    <td><strong>upload_media</strong></td>
                    <td><span class="method-post">POST</span></td>
                    <td><code>/media</code></td>
                    <td>Upload media files</td>
                </tr>
                <tr>
                    <td><strong>set_featured_image</strong></td>
                    <td><span class="method-put">PUT</span></td>
                    <td><code>/post/{id}/featured-image</code></td>
                    <td>Set post featured image</td>
                </tr>
                <tr>
                    <td><strong>activity_log</strong></td>
                    <td><span class="method-get">GET</span></td>
                    <td><code>/activity-log</code></td>
                    <td>Get activity log (admin only)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Security Tips -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('🔒 Security Tips', 'webweaver'); ?></h2>
        <div class="webweaver-card">
            <ul class="tips-list">
                <li><strong>Use HTTPS:</strong> Always use HTTPS in production for API requests</li>
                <li><strong>Application Passwords:</strong> Use app-specific passwords instead of user passwords</li>
                <li><strong>Rate Limiting:</strong> API is rate-limited to <?php echo esc_html($rate_limit); ?> requests per hour per user</li>
                <li><strong>Draft-Only Mode:</strong> When enabled, all created posts start as drafts</li>
                <li><strong>Permissions:</strong> API respects WordPress user roles and capabilities</li>
                <li><strong>Audit Trail:</strong> All actions are logged in the Activity Log for security review</li>
            </ul>
        </div>
    </div>

    <!-- Support -->
    <div class="webweaver-section">
        <h2><?php esc_html_e('📞 Support', 'webweaver'); ?></h2>
        <div class="webweaver-card">
            <p><strong>Version:</strong> <?php echo esc_html($version); ?></p>
            <p><strong>Documentation:</strong> Check the README.md file for detailed guides</p>
            <p><strong>Activity Log:</strong> Review the <a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-activity')); ?>">Activity Log</a> for debugging API interactions</p>
            <p><strong>Settings:</strong> Adjust plugin behavior in <a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-settings')); ?>">Settings</a></p>
        </div>
    </div>
</div>

<style>
    .webweaver-dashboard {
        max-width: 1200px;
    }

    .subtitle {
        font-size: 16px;
        color: #666;
        margin: -10px 0 30px;
    }

    .webweaver-section {
        margin-bottom: 40px;
    }

    .webweaver-section h2 {
        border-bottom: 3px solid #2271b1;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-size: 24px;
    }

    .webweaver-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .webweaver-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.3s ease;
    }

    .webweaver-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header h3 {
        margin: 0 0 15px 0;
        font-size: 18px;
        color: #333;
    }

    .status-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .status-list li {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .status-list li:last-child {
        border-bottom: none;
    }

    .status-list .label {
        font-weight: 500;
        color: #333;
    }

    .status-list .value {
        color: #666;
        word-break: break-all;
    }

    .status-success {
        color: #27ae60;
        font-weight: 500;
    }

    .status-warning {
        color: #f39c12;
        font-weight: 500;
    }

    .status-info {
        color: #2271b1;
        font-weight: 500;
    }

    .quick-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .quick-actions .button {
        padding: 8px 16px;
        font-size: 14px;
    }

    .setup-steps {
        line-height: 1.8;
    }

    .setup-steps li {
        margin-bottom: 20px;
    }

    .setup-steps strong {
        font-size: 16px;
    }

    .setup-steps p {
        margin: 8px 0;
    }

    .code-block {
        background: #f5f5f5;
        border-left: 4px solid #2271b1;
        padding: 12px 15px;
        margin: 10px 0;
        border-radius: 4px;
        font-family: monospace;
        font-size: 13px;
        line-height: 1.6;
    }

    .api-reference {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    .api-reference thead {
        background: #f9f9f9;
        border-bottom: 2px solid #e0e0e0;
    }

    .api-reference th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #333;
    }

    .api-reference td {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .api-reference tr:last-child td {
        border-bottom: none;
    }

    .api-reference code {
        background: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
    }

    .method-get {
        background: #d4edda;
        color: #155724;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .method-post {
        background: #cfe2ff;
        color: #084298;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .method-put {
        background: #fff3cd;
        color: #856404;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 600;
    }

    .tips-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .tips-list li {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .tips-list li:last-child {
        border-bottom: none;
    }

    .tips-list strong {
        color: #2271b1;
    }

    code {
        background: #f1f1f1;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .webweaver-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            flex-direction: column;
        }

        .quick-actions .button {
            width: 100%;
        }

        .api-reference {
            font-size: 13px;
        }

        .api-reference th,
        .api-reference td {
            padding: 8px 10px;
        }
    }
</style>
