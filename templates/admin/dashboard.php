<?php
/**
 * Dashboard Template
 */

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized'));
}

use WP_MCP_Connector\Builders;
?>

<div class="wrap">
    <h1><?php esc_html_e('WebWeaver Dashboard'); ?></h1>

    <div class="webweaver-grid">
        <!-- Status Cards -->
        <div class="webweaver-card">
            <h2><?php esc_html_e('Status'); ?></h2>
            <ul>
                <li>
                    <strong><?php esc_html_e('Plugin Version:'); ?></strong>
                    <?php echo esc_html(WEBWEAVER_VERSION); ?>
                </li>
                <li>
                    <strong><?php esc_html_e('HTTPS:'); ?></strong>
                    <?php echo is_ssl() ? '✅ Enabled' : '⚠️ Disabled'; ?>
                </li>
                <li>
                    <strong><?php esc_html_e('Draft-Only Mode:'); ?></strong>
                    <?php echo get_option('webweaver_draft_only') ? '✅ On' : '❌ Off'; ?>
                </li>
            </ul>
        </div>

        <!-- Builders Status -->
        <div class="webweaver-card">
            <h2><?php esc_html_e('Supported Builders'); ?></h2>
            <ul>
                <?php foreach (Builders\Registry::get_active_builders() as $builder => $info) : ?>
                    <li>
                        <strong><?php echo esc_html($info['name']); ?>:</strong>
                        ✅ Active
                        <?php if (!empty($info['version'])) {
                            echo '(v' . esc_html($info['version']) . ')';
                        } ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Quick Actions -->
        <div class="webweaver-card">
            <h2><?php esc_html_e('Quick Actions'); ?></h2>
            <ul>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-settings')); ?>"><?php esc_html_e('⚙️ Settings'); ?></a></li>
                <li><a href="<?php echo esc_url(admin_url('admin.php?page=webweaver-activity')); ?>"><?php esc_html_e('📋 Activity Log'); ?></a></li>
                <li><a href="<?php echo esc_url(rest_url('wp-mcp/v1/tools')); ?>" target="_blank"><?php esc_html_e('📡 Tools Manifest'); ?></a></li>
            </ul>
        </div>
    </div>

    <hr>

    <h2><?php esc_html_e('API Documentation'); ?></h2>
    <p><?php esc_html_e('Base URL: '); ?><code><?php echo esc_html(rest_url('wp-mcp/v1')); ?></code></p>
    <p><?php esc_html_e('Fetch the tools manifest at: '); ?><a href="<?php echo esc_url(rest_url('wp-mcp/v1/tools')); ?>" target="_blank">/tools</a></p>
</div>

<style>
    .webweaver-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .webweaver-card {
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .webweaver-card h2 {
        margin-top: 0;
        font-size: 18px;
    }

    .webweaver-card ul {
        margin: 0;
        padding-left: 20px;
    }

    .webweaver-card li {
        margin-bottom: 10px;
    }

    .webweaver-card a {
        color: #2271b1;
        text-decoration: none;
    }

    .webweaver-card a:hover {
        text-decoration: underline;
    }

    code {
        background: #f1f1f1;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
    }
</style>
