<?php
/**
 * Divi Builder Support
 */

namespace WP_MCP_Connector\Builders;

class Divi {
    public static function set_layout($post_id, $payload) {
        if (!isset($payload['content'])) {
            return;
        }

        $content = $payload['content'];
        $mode = $payload['mode'] ?? 'replace';

        if ($mode === 'append') {
            $post = get_post($post_id);
            $content = $post->post_content . $content;
        }

        // Sanitize: allow Divi shortcodes.
        $sanitized_content = self::sanitize_divi_content($content);

        wp_update_post([
            'ID' => $post_id,
            'post_content' => $sanitized_content,
        ]);

        // Mark as Divi.
        update_post_meta($post_id, '_et_pb_use_builder', 'on');
    }

    public static function get_layout($post_id) {
        $post = get_post($post_id);
        return $post->post_content;
    }

    private static function sanitize_divi_content($content) {
        // Allow Divi shortcodes.
        $allowed = [
            'et_pb_section' => [],
            'et_pb_row' => [],
            'et_pb_column' => [],
            'et_pb_text' => [],
            'et_pb_image' => [],
            'et_pb_button' => [],
            'et_pb_cta' => [],
        ];

        return wp_kses_post($content);
    }
}
