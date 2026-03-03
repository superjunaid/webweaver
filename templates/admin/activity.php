<?php
/**
 * Activity Log Template
 */

if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized'));
}

global $wpdb;

$page = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Get logs.
$logs = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}mcp_activity_log ORDER BY timestamp DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    )
);

// Get total.
$total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mcp_activity_log");
$total_pages = ceil($total / $per_page);
?>

<div class="wrap">
    <h1><?php esc_html_e('WebWeaver Activity Log'); ?></h1>

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Timestamp'); ?></th>
                <th><?php esc_html_e('User'); ?></th>
                <th><?php esc_html_e('Action'); ?></th>
                <th><?php esc_html_e('Post ID'); ?></th>
                <th><?php esc_html_e('Builder'); ?></th>
                <th><?php esc_html_e('Result'); ?></th>
                <th><?php esc_html_e('IP'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($logs) : ?>
                <?php foreach ($logs as $log) : ?>
                    <tr>
                        <td><?php echo esc_html($log->timestamp); ?></td>
                        <td><?php echo esc_html(get_the_author_meta('display_name', $log->user_id)); ?></td>
                        <td><?php echo esc_html($log->action); ?></td>
                        <td>
                            <?php if ($log->post_id) : ?>
                                <a href="<?php echo esc_url(get_edit_post_link($log->post_id)); ?>" target="_blank">
                                    <?php echo esc_html($log->post_id); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($log->builder_mode); ?></td>
                        <td>
                            <span style="color: <?php echo $log->result === 'success' ? 'green' : 'red'; ?>">
                                <?php echo esc_html($log->result); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($log->ip_address); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7"><?php esc_html_e('No activity logged yet.'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1) : ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="<?php echo esc_url(add_query_arg('paged', $i)); ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>>
                    <?php echo esc_html($i); ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

    <p><?php echo esc_html("Total: $total entries"); ?></p>
</div>

<style>
    .pagination {
        margin-top: 20px;
        display: flex;
        gap: 10px;
    }

    .pagination a {
        padding: 5px 10px;
        background: #f1f1f1;
        border: 1px solid #ccc;
        text-decoration: none;
        color: #2271b1;
    }

    .pagination a.active {
        background: #2271b1;
        color: white;
    }
</style>
