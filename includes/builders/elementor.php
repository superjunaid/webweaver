<?php
/**
 * Elementor Builder Support
 */

namespace WP_MCP_Connector\Builders;

class Elementor {
    public static function set_layout($post_id, $payload) {
        if (!isset($payload['elementor_json'])) {
            return;
        }

        $mode = $payload['mode'] ?? 'replace';
        $elementor_json = $payload['elementor_json'];

        if (is_array($elementor_json)) {
            $elementor_json = json_encode($elementor_json);
        }

        // Validate JSON.
        $decoded = json_decode($elementor_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid Elementor JSON');
        }

        if ($mode === 'merge') {
            $existing = get_post_meta($post_id, '_elementor_data', true);
            if ($existing) {
                $existing_data = json_decode($existing, true);
                $elementor_json = json_encode(array_merge($existing_data, $decoded));
            }
        }

        // Save Elementor data.
        update_post_meta($post_id, '_elementor_data', $elementor_json);
        update_post_meta($post_id, '_elementor_edit_mode', 'builder');

        // Trigger Elementor refresh.
        if (function_exists('elementor_pro_flush_css_on_posts')) {
            elementor_pro_flush_css_on_posts([$post_id]);
        }

        // Empty post_content for Elementor pages (it will render from meta).
        wp_update_post([
            'ID' => $post_id,
            'post_content' => '',
        ]);
    }

    public static function get_layout($post_id) {
        $data = get_post_meta($post_id, '_elementor_data', true);
        return $data ? json_decode($data, true) : [];
    }
}
