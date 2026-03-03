<?php
/**
 * Settings Template
 */

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized'));
}

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['webweaver_settings_nonce'])) {
    check_admin_referer('webweaver_settings', 'webweaver_settings_nonce');

    update_option('webweaver_draft_only', isset($_POST['draft_only']) ? 1 : 0);
    update_option('webweaver_rate_limit', (int)$_POST['rate_limit']);

    $allowed_types = isset($_POST['allowed_post_types']) ? (array)$_POST['allowed_post_types'] : [];
    update_option('webweaver_allowed_post_types', $allowed_types);

    $protected = isset($_POST['protected_pages']) ? sanitize_textarea_field($_POST['protected_pages']) : '';
    $protected_ids = array_filter(array_map('intval', explode(',', $protected)));
    update_option('webweaver_protected_pages', $protected_ids);

    echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
}

$draft_only = get_option('webweaver_draft_only', 1);
$rate_limit = get_option('webweaver_rate_limit', 60);
$allowed_types = get_option('webweaver_allowed_post_types', ['post', 'page']);
$protected_pages = implode(',', (array)get_option('webweaver_protected_pages', []));
?>

<div class="wrap">
    <h1><?php esc_html_e('WebWeaver Settings'); ?></h1>

    <form method="POST" class="webweaver-settings-form">
        <?php wp_nonce_field('webweaver_settings', 'webweaver_settings_nonce'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="draft_only"><?php esc_html_e('Draft-Only Mode'); ?></label></th>
                <td>
                    <input type="checkbox" id="draft_only" name="draft_only" value="1" <?php checked($draft_only); ?>>
                    <p class="description"><?php esc_html_e('If enabled, AI can only create/update drafts, preventing direct publication.'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="rate_limit"><?php esc_html_e('Rate Limit (per hour)'); ?></label></th>
                <td>
                    <input type="number" id="rate_limit" name="rate_limit" value="<?php echo esc_attr($rate_limit); ?>" min="1" step="1">
                    <p class="description"><?php esc_html_e('Maximum write operations per user per hour.'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php esc_html_e('Allowed Post Types'); ?></th>
                <td>
                    <label><input type="checkbox" name="allowed_post_types[]" value="post" <?php checked(in_array('post', $allowed_types)); ?>> Post</label><br>
                    <label><input type="checkbox" name="allowed_post_types[]" value="page" <?php checked(in_array('page', $allowed_types)); ?>> Page</label>
                    <p class="description"><?php esc_html_e('Select which post types AI can create/edit.'); ?></p>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="protected_pages"><?php esc_html_e('Protected Page IDs'); ?></label></th>
                <td>
                    <textarea id="protected_pages" name="protected_pages" rows="4" class="large-text"><?php echo esc_textarea($protected_pages); ?></textarea>
                    <p class="description"><?php esc_html_e('Comma-separated list of post IDs that AI cannot edit (e.g., 1,2,3).'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>

<style>
    .webweaver-settings-form {
        background: white;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
</style>
