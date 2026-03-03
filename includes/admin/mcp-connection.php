<?php
/**
 * MCP Connection Helper
 */

namespace WP_MCP_Connector\Admin;

use WP_MCP_Connector\API\Auth\Authenticate;

class MCPConnection {
    public static function register_menu() {
        add_submenu_page(
            'webweaver',
            'MCP Connection',
            'MCP Connection',
            'manage_options',
            'webweaver-mcp',
            [self::class, 'render_page']
        );
    }

    public static function render_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $current_user = wp_get_current_user();
        $site_url = get_site_url();
        $api_url = rest_url('wp-mcp/v1');
        
        ?>
        <div class="wrap webweaver-mcp-connection">
            <h1><?php esc_html_e('🔗 MCP Connection Helper', 'webweaver'); ?></h1>
            
            <div class="webweaver-tabs">
                <button class="tab-button active" data-tab="manus"><?php esc_html_e('Manus.im', 'webweaver'); ?></button>
                <button class="tab-button" data-tab="claude"><?php esc_html_e('Claude', 'webweaver'); ?></button>
                <button class="tab-button" data-tab="custom"><?php esc_html_e('Other MCP', 'webweaver'); ?></button>
                <button class="tab-button" data-tab="api-keys"><?php esc_html_e('API Keys', 'webweaver'); ?></button>
            </div>

            <!-- Manus.im Tab -->
            <div id="manus-tab" class="tab-content active">
                <?php self::render_manus_section($current_user, $api_url); ?>
            </div>

            <!-- Claude Tab -->
            <div id="claude-tab" class="tab-content">
                <?php self::render_claude_section($current_user, $api_url); ?>
            </div>

            <!-- Custom MCP Tab -->
            <div id="custom-tab" class="tab-content">
                <?php self::render_custom_section($current_user, $api_url); ?>
            </div>

            <!-- API Keys Tab -->
            <div id="api-keys-tab" class="tab-content">
                <?php self::render_api_keys_section($current_user); ?>
            </div>
        </div>

        <style>
            .webweaver-mcp-connection {
                max-width: 900px;
            }

            .webweaver-tabs {
                display: flex;
                gap: 10px;
                margin: 20px 0;
                border-bottom: 2px solid #e0e0e0;
            }

            .tab-button {
                padding: 10px 20px;
                border: none;
                background: transparent;
                cursor: pointer;
                font-size: 14px;
                border-bottom: 3px solid transparent;
                transition: all 0.3s;
            }

            .tab-button.active {
                border-bottom-color: #2271b1;
                color: #2271b1;
                font-weight: bold;
            }

            .tab-button:hover {
                color: #2271b1;
            }

            .tab-content {
                display: none;
                padding: 20px 0;
            }

            .tab-content.active {
                display: block;
            }

            .mcp-card {
                background: #fff;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .mcp-card h3 {
                margin-top: 0;
                color: #2271b1;
            }

            .code-box {
                background: #f5f5f5;
                border-left: 4px solid #2271b1;
                padding: 15px;
                margin: 15px 0;
                border-radius: 4px;
                font-family: monospace;
                font-size: 13px;
                position: relative;
            }

            .code-box .copy-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                padding: 5px 10px;
                background: #2271b1;
                color: white;
                border: none;
                border-radius: 3px;
                cursor: pointer;
                font-size: 12px;
            }

            .code-box .copy-btn:hover {
                background: #135e96;
            }

            .code-box .copied {
                color: #27ae60;
            }

            .step {
                margin-bottom: 20px;
            }

            .step-number {
                display: inline-block;
                width: 30px;
                height: 30px;
                background: #2271b1;
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 30px;
                font-weight: bold;
                margin-right: 10px;
            }

            .step-title {
                font-weight: bold;
                margin: 10px 0 5px 40px;
            }

            .step-content {
                margin-left: 40px;
                color: #666;
            }

            .auth-method {
                background: #f9f9f9;
                border-left: 4px solid #ddd;
                padding: 15px;
                margin: 10px 0;
                border-radius: 4px;
            }

            .auth-method.recommended {
                border-left-color: #27ae60;
                background: #f0f8f4;
            }

            .auth-method .label {
                font-weight: bold;
                color: #333;
            }

            .auth-method .value {
                font-family: monospace;
                background: white;
                padding: 8px;
                margin: 8px 0;
                border-radius: 3px;
                border: 1px solid #ddd;
                word-break: break-all;
            }

            .button-group {
                display: flex;
                gap: 10px;
                margin-top: 15px;
            }

            .info-box {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
                padding: 12px;
                border-radius: 4px;
                margin: 15px 0;
            }

            .warning-box {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
                padding: 12px;
                border-radius: 4px;
                margin: 15px 0;
            }

            table.api-keys {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            table.api-keys th,
            table.api-keys td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #e0e0e0;
            }

            table.api-keys th {
                background: #f5f5f5;
                font-weight: bold;
            }

            .btn-generate {
                background: #27ae60;
                color: white;
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }

            .btn-generate:hover {
                background: #229954;
            }

            .btn-revoke {
                background: #e74c3c;
                color: white;
                padding: 5px 10px;
                border: none;
                border-radius: 3px;
                cursor: pointer;
                font-size: 12px;
            }

            .btn-revoke:hover {
                background: #c0392b;
            }

            .api-key-display {
                background: #f5f5f5;
                padding: 15px;
                border-radius: 4px;
                margin: 15px 0;
                font-family: monospace;
                word-break: break-all;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .api-key-value {
                flex: 1;
            }

            .api-key-copy {
                margin-left: 10px;
            }
        </style>

        <script>
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Hide all tabs
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    document.querySelectorAll('.tab-button').forEach(b => {
                        b.classList.remove('active');
                    });

                    // Show selected tab
                    const tabId = this.dataset.tab + '-tab';
                    document.getElementById(tabId).classList.add('active');
                    this.classList.add('active');
                });
            });

            // Copy to clipboard
            document.querySelectorAll('.copy-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const codeBox = this.closest('.code-box');
                    const text = codeBox.textContent.replace('Copy', '').trim();
                    
                    navigator.clipboard.writeText(text).then(() => {
                        const original = this.textContent;
                        this.textContent = 'Copied! ✓';
                        this.classList.add('copied');
                        
                        setTimeout(() => {
                            this.textContent = original;
                            this.classList.remove('copied');
                        }, 2000);
                    });
                });
            });
        </script>
        <?php
    }

    private static function render_manus_section($current_user, $api_url) {
        ?>
        <div class="mcp-card">
            <h3><?php esc_html_e('🎯 Quick Setup for Manus.im', 'webweaver'); ?></h3>
            
            <div class="info-box">
                <strong><?php esc_html_e('Recommended:', 'webweaver'); ?></strong>
                <?php esc_html_e('Use X-API-Key method (easier and more secure)', 'webweaver'); ?>
            </div>

            <h4><?php esc_html_e('Option A: X-API-Key (RECOMMENDED)', 'webweaver'); ?></h4>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <p><?php esc_html_e('Click button below to generate API key:', 'webweaver'); ?></p>
                    <form method="post">
                        <?php wp_nonce_field('generate_api_key'); ?>
                        <button type="submit" name="action" value="generate_api_key" class="btn-generate">
                            <?php esc_html_e('Generate API Key', 'webweaver'); ?>
                        </button>
                    </form>
                    <?php 
                    if (isset($_POST['action']) && $_POST['action'] === 'generate_api_key' && wp_verify_nonce($_POST['_wpnonce'], 'generate_api_key')) {
                        $api_key = Authenticate::generate_api_key(get_current_user_id());
                        ?>
                        <div class="api-key-display">
                            <div class="api-key-value"><?php echo esc_html($api_key); ?></div>
                            <button class="copy-btn" onclick="copyToClipboard(this)">Copy</button>
                        </div>
                        <p style="color: #27ae60;"><strong><?php esc_html_e('✓ API Key generated! Copy it above.', 'webweaver'); ?></strong></p>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <p><?php esc_html_e('Add to Manus.im:', 'webweaver'); ?></p>
                    <div class="code-box">
                        Server URL: <?php echo esc_html($api_url); ?>
                        <br><br>
                        Add custom header:<br>
                        Header Name: X-API-Key<br>
                        Header Value: [paste your API key here]
                        <button class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <p><?php esc_html_e('Click "Try it out" in Manus.im → Done! ✓', 'webweaver'); ?></p>
                </div>
            </div>

            <hr style="margin: 30px 0;">

            <h4><?php esc_html_e('Option B: Basic Auth', 'webweaver'); ?></h4>
            <p><?php esc_html_e('If you prefer Basic Auth method:', 'webweaver'); ?></p>
            
            <div class="auth-method">
                <div class="label">Server URL:</div>
                <div class="value"><?php echo esc_html($api_url); ?></div>
            </div>

            <div class="auth-method">
                <div class="label">Header Name:</div>
                <div class="value">Authorization</div>
            </div>

            <div class="auth-method">
                <div class="label">Header Value:</div>
                <div class="value" id="basic-auth-header">
                    <?php echo esc_html('Basic ' . base64_encode($current_user->user_login . ':' . '*password*')); ?>
                </div>
                <p style="color: #999; font-size: 12px;">
                    <?php esc_html_e('Replace *password* with your WordPress password or app password', 'webweaver'); ?>
                </p>
            </div>

            <div class="warning-box">
                <strong><?php esc_html_e('⚠️ Security:', 'webweaver'); ?></strong>
                <?php esc_html_e('X-API-Key is more secure. Use app password if using Basic Auth.', 'webweaver'); ?>
            </div>
        </div>
        <?php
    }

    private static function render_claude_section($current_user, $api_url) {
        ?>
        <div class="mcp-card">
            <h3><?php esc_html_e('Claude Integration', 'webweaver'); ?></h3>
            
            <div class="info-box">
                <?php esc_html_e('Claude uses MCP servers. You have two options:', 'webweaver'); ?>
            </div>

            <h4><?php esc_html_e('Option 1: Direct REST API (Recommended)', 'webweaver'); ?></h4>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <p><?php esc_html_e('Generate API Key above (see Manus.im tab)', 'webweaver'); ?></p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <p><?php esc_html_e('Use with X-API-Key header:', 'webweaver'); ?></p>
                    <div class="code-box">
                        curl -H "X-API-Key: wpmc_your_key_here" \
                        <?php echo esc_html($api_url); ?>/tools
                        <button class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>

            <hr style="margin: 30px 0;">

            <h4><?php esc_html_e('Option 2: MCP Server Wrapper', 'webweaver'); ?></h4>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <p><?php esc_html_e('Run MCP server on your machine:', 'webweaver'); ?></p>
                    <div class="code-box">
                        cd /path/to/webweaver<br>
                        node .tools/mcp-server.js
                        <button class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <p><?php esc_html_e('In Claude, add custom connector:', 'webweaver'); ?></p>
                    <div class="code-box">
                        Name: WebWeaver<br>
                        URL: http://localhost:3000
                        <button class="copy-btn">Copy</button>
                    </div>
                </div>
            </div>

            <div class="info-box">
                <?php esc_html_e('See .docs/MCP_CLAUDE_SETUP.md for detailed instructions', 'webweaver'); ?>
            </div>
        </div>
        <?php
    }

    private static function render_custom_section($current_user, $api_url) {
        ?>
        <div class="mcp-card">
            <h3><?php esc_html_e('Custom MCP Client Setup', 'webweaver'); ?></h3>
            
            <p><?php esc_html_e('WebWeaver supports multiple authentication methods. Choose one:', 'webweaver'); ?></p>

            <h4><?php esc_html_e('Supported Authentication Methods', 'webweaver'); ?></h4>

            <div class="auth-method recommended">
                <div class="label">✅ Method 1: X-API-Key (RECOMMENDED)</div>
                <div class="value">Header: X-API-Key<br>Value: wpmc_[your-api-key]</div>
                <p><?php esc_html_e('Most secure. Generate key above (Manus.im tab).', 'webweaver'); ?></p>
            </div>

            <div class="auth-method">
                <div class="label">Method 2: Bearer Token</div>
                <div class="value">Header: Authorization<br>Value: Bearer wpmc_[your-api-key]</div>
                <p><?php esc_html_e('OAuth-style. Use same API key as X-API-Key method.', 'webweaver'); ?></p>
            </div>

            <div class="auth-method">
                <div class="label">Method 3: Basic Auth</div>
                <div class="value">Header: Authorization<br>Value: Basic [base64-encoded]</div>
                <p><?php esc_html_e('Username:Password format (less secure).', 'webweaver'); ?></p>
            </div>

            <div class="auth-method">
                <div class="label">Method 4: Session/Cookie</div>
                <p><?php esc_html_e('Inherit from WordPress login session.', 'webweaver'); ?></p>
            </div>

            <h4><?php esc_html_e('API Endpoint', 'webweaver'); ?></h4>
            
            <div class="code-box">
                <?php echo esc_html($api_url); ?>
                <button class="copy-btn">Copy</button>
            </div>

            <h4><?php esc_html_e('Available Tools', 'webweaver'); ?></h4>
            <div style="background: #f5f5f5; padding: 15px; border-radius: 4px;">
                <code>
                    GET /tools - List available tools<br>
                    GET /posts - List posts<br>
                    GET /post/{id} - Get post<br>
                    POST /post - Create post<br>
                    PUT /post/{id} - Update post<br>
                    POST /media - Upload media<br>
                    PUT /post/{id}/featured-image - Set image<br>
                    GET /activity-log - View log
                </code>
            </div>

            <h4><?php esc_html_e('Test Connection', 'webweaver'); ?></h4>
            <div class="code-box">
                curl -H "X-API-Key: wpmc_your_key" \<br>
                <?php echo esc_html($api_url); ?>/tools
                <button class="copy-btn">Copy</button>
            </div>
        </div>
        <?php
    }

    private static function render_api_keys_section($current_user) {
        $user_keys = Authenticate::get_user_api_keys($current_user->ID);
        ?>
        <div class="mcp-card">
            <h3><?php esc_html_e('🔑 API Key Management', 'webweaver'); ?></h3>
            
            <div class="button-group">
                <form method="post">
                    <?php wp_nonce_field('generate_api_key'); ?>
                    <button type="submit" name="action" value="generate_api_key" class="btn-generate">
                        <?php esc_html_e('+ Generate New API Key', 'webweaver'); ?>
                    </button>
                </form>
            </div>

            <?php if (!empty($user_keys)): ?>
                <h4><?php esc_html_e('Your API Keys', 'webweaver'); ?></h4>
                <table class="api-keys">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Key', 'webweaver'); ?></th>
                            <th><?php esc_html_e('Created', 'webweaver'); ?></th>
                            <th><?php esc_html_e('Action', 'webweaver'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($user_keys as $key): ?>
                            <tr>
                                <td>
                                    <code style="word-break: break-all;">
                                        <?php echo esc_html(substr($key, 0, 10) . '...' . substr($key, -10)); ?>
                                    </code>
                                </td>
                                <td><?php esc_html_e('WebWeaver API Key', 'webweaver'); ?></td>
                                <td>
                                    <form method="post" style="display: inline;">
                                        <?php wp_nonce_field('revoke_api_key'); ?>
                                        <input type="hidden" name="api_key" value="<?php echo esc_attr($key); ?>">
                                        <button type="submit" name="action" value="revoke_api_key" class="btn-revoke" onclick="return confirm('<?php esc_attr_e('Revoke this key?', 'webweaver'); ?>')">
                                            <?php esc_html_e('Revoke', 'webweaver'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php esc_html_e('No API keys yet. Generate one to get started!', 'webweaver'); ?></p>
            <?php endif; ?>

            <div class="info-box">
                <strong><?php esc_html_e('💡 Tip:', 'webweaver'); ?></strong>
                <?php esc_html_e('Generate a new key for each MCP client or service.', 'webweaver'); ?>
            </div>
        </div>
        <?php
    }
}
?>
