<?php
/**
 * Gutenberg Builder Support
 */

namespace WP_MCP_Connector\Builders;

class Gutenberg {
    public static function set_content($post_id, $payload) {
        if (!isset($payload['content'])) {
            return;
        }

        $content = $payload['content'];

        // Sanitize HTML with wp_kses_post.
        $sanitized_content = wp_kses_post($content);

        wp_update_post([
            'ID' => $post_id,
            'post_content' => $sanitized_content,
        ]);

        // Mark as Gutenberg.
        update_post_meta($post_id, '_wp_page_template', 'default');
    }

    public static function get_content($post_id) {
        $post = get_post($post_id);
        return $post->post_content;
    }
}
