<?php
/**
 * Builder Factory
 */

namespace WP_MCP_Connector\Builders;

class Factory {
    public static function detect_builder($post_id) {
        // Check Elementor.
        if (get_post_meta($post_id, '_elementor_edit_mode', true) === 'builder') {
            return 'elementor';
        }

        // Check Divi.
        if (get_post_meta($post_id, '_et_pb_use_builder', true) === 'on') {
            return 'divi';
        }

        // Default to Gutenberg.
        return 'gutenberg';
    }

    public static function set_content($post_id, $builder, $payload) {
        switch ($builder) {
            case 'elementor':
                Elementor::set_layout($post_id, $payload);
                break;

            case 'divi':
                Divi::set_layout($post_id, $payload);
                break;

            case 'gutenberg':
            default:
                Gutenberg::set_content($post_id, $payload);
                break;
        }
    }

    public static function get_content($post_id) {
        $builder = self::detect_builder($post_id);
        return self::set_content($post_id, $builder, []);
    }
}
