<?php
/**
 * Posts Endpoint
 */

namespace WP_MCP_Connector\API\Endpoints;

use WP_MCP_Connector\Builders;
use WP_MCP_Connector\Logging;
use WP_MCP_Connector\Security;

class Posts {
    public static function list_posts(\WP_REST_Request $request) {
        $type = $request->get_param('type') ?: 'post';
        $status = $request->get_param('status') ?: 'any';
        $builder = $request->get_param('builder') ?: '';
        $search = $request->get_param('search') ?: '';
        $per_page = (int)$request->get_param('per_page') ?: 20;
        $page = (int)$request->get_param('page') ?: 1;

        // Check permissions.
        if (!current_user_can('read_posts')) {
            return new \WP_Error('forbidden', 'Insufficient permissions', ['status' => 403]);
        }

        // Check policy: allowed post types.
        if (!Security\Policy::is_allowed_post_type($type)) {
            return new \WP_Error('forbidden', 'Post type not allowed', ['status' => 403]);
        }

        $args = [
            'post_type' => $type,
            'status' => $status,
            'posts_per_page' => $per_page,
            'paged' => $page,
            's' => $search,
        ];

        $posts = get_posts($args);
        $total = count(get_posts(array_merge($args, ['posts_per_page' => -1, 'paged' => 1])));

        $data = [];
        foreach ($posts as $post) {
            $data[] = self::post_to_response($post, $builder);
        }

        return rest_ensure_response([
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
        ]);
    }

    public static function get_post(\WP_REST_Request $request) {
        $post_id = (int)$request->get_param('id');
        $post = get_post($post_id);

        if (!$post) {
            return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
        }

        if (!current_user_can('read_post', $post_id)) {
            return new \WP_Error('forbidden', 'Cannot read this post', ['status' => 403]);
        }

        if (!Security\Policy::is_post_allowed($post_id)) {
            return new \WP_Error('forbidden', 'Post is protected', ['status' => 403]);
        }

        return rest_ensure_response(self::post_to_response($post, ''));
    }

    public static function create_post(\WP_REST_Request $request) {
        // Check rate limit.
        if (!Security\RateLimit::check_limit(get_current_user_id())) {
            return new \WP_Error('rate_limit', 'Rate limit exceeded', ['status' => 429]);
        }

        $body = $request->get_json_params();

        // Validate input.
        if (empty($body['title'])) {
            return new \WP_Error('invalid', 'Title is required', ['status' => 400]);
        }

        $type = $body['type'] ?? 'post';
        $status = isset($body['status']) ? $body['status'] : 'draft';

        // Security: enforce draft-only mode if enabled.
        if (Security\Policy::is_draft_only_mode()) {
            $status = 'draft';
        }

        // Check permissions.
        if (!current_user_can("create_posts") || !current_user_can("edit_{$type}s")) {
            return new \WP_Error('forbidden', 'Insufficient permissions', ['status' => 403]);
        }

        // Check policy: allowed post types.
        if (!Security\Policy::is_allowed_post_type($type)) {
            return new \WP_Error('forbidden', 'Post type not allowed', ['status' => 403]);
        }

        // Create post.
        $post_data = [
            'post_title' => sanitize_text_field($body['title']),
            'post_content' => '',
            'post_excerpt' => sanitize_text_field($body['excerpt'] ?? ''),
            'post_type' => $type,
            'post_status' => $status,
            'post_name' => sanitize_title($body['slug'] ?? ''),
        ];

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return new \WP_Error('create_failed', 'Failed to create post', ['status' => 500]);
        }

        // Handle builder content.
        $builder_mode = $body['builder_mode'] ?? 'gutenberg';
        if (!empty($body['builder_payload'])) {
            Builders\Factory::set_content($post_id, $builder_mode, $body['builder_payload']);
        }

        // Set meta.
        if (!empty($body['meta']) && is_array($body['meta'])) {
            foreach ($body['meta'] as $key => $value) {
                if (Security\Meta::is_allowed_meta_key($key)) {
                    update_post_meta($post_id, $key, sanitize_meta($key, $value, 'post'));
                }
            }
        }

        // Log activity.
        Logging\Activity::log($post_id, 'create', 'success', $builder_mode);

        $post = get_post($post_id);
        return rest_ensure_response(self::post_to_response($post, $builder_mode));
    }

    public static function update_post(\WP_REST_Request $request) {
        // Check rate limit.
        if (!Security\RateLimit::check_limit(get_current_user_id())) {
            return new \WP_Error('rate_limit', 'Rate limit exceeded', ['status' => 429]);
        }

        $post_id = (int)$request->get_param('id');
        $post = get_post($post_id);

        if (!$post) {
            return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
        }

        // Check permissions.
        if (!current_user_can('edit_post', $post_id)) {
            return new \WP_Error('forbidden', 'Cannot edit this post', ['status' => 403]);
        }

        if (!Security\Policy::is_post_allowed($post_id)) {
            return new \WP_Error('forbidden', 'Post is protected', ['status' => 403]);
        }

        // Create revision before update.
        wp_save_post_revision($post_id);

        $body = $request->get_json_params();

        // Prepare update data.
        $post_data = ['ID' => $post_id];
        if (!empty($body['title'])) {
            $post_data['post_title'] = sanitize_text_field($body['title']);
        }
        if (isset($body['status'])) {
            $status = $body['status'];
            // Enforce draft-only mode.
            if (Security\Policy::is_draft_only_mode() && $status !== 'draft') {
                $status = 'draft';
            }
            $post_data['post_status'] = $status;
        }
        if (!empty($body['excerpt'])) {
            $post_data['post_excerpt'] = sanitize_text_field($body['excerpt']);
        }

        wp_update_post($post_data);

        // Handle builder content.
        $builder_mode = $body['builder_mode'] ?? Builders\Factory::detect_builder($post_id);
        if (!empty($body['builder_payload'])) {
            Builders\Factory::set_content($post_id, $builder_mode, $body['builder_payload']);
        }

        // Update meta.
        if (!empty($body['meta']) && is_array($body['meta'])) {
            foreach ($body['meta'] as $key => $value) {
                if (Security\Meta::is_allowed_meta_key($key)) {
                    update_post_meta($post_id, $key, sanitize_meta($key, $value, 'post'));
                }
            }
        }

        // Log activity.
        Logging\Activity::log($post_id, 'update', 'success', $builder_mode);

        $post = get_post($post_id);
        return rest_ensure_response(self::post_to_response($post, $builder_mode));
    }

    private static function post_to_response($post, $builder = '') {
        if (!$builder) {
            $builder = Builders\Factory::detect_builder($post->ID);
        }

        $response = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'type' => $post->post_type,
            'builder_mode' => $builder,
            'edit_url' => get_edit_post_link($post->ID, 'raw'),
            'view_url' => get_permalink($post->ID),
        ];

        if ($builder === 'elementor') {
            $elementor_data = get_post_meta($post->ID, '_elementor_data', true);
            if ($elementor_data) {
                $response['builder_content'] = json_decode($elementor_data, true);
            }
        } elseif ($builder === 'divi') {
            $response['builder_content'] = $post->post_content;
        } else {
            $response['builder_content'] = $post->post_content;
        }

        return $response;
    }
}
