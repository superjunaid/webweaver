<?php
/**
 * Tools Manifest Endpoint
 */

namespace WP_MCP_Connector\API\Endpoints;

use WP_MCP_Connector\Builders;

class Tools {
    public static function get_manifest(\WP_REST_Request $request) {
        $manifest = [
            'version' => WEBWEAVER_VERSION,
            'site_url' => get_site_url(),
            'builders' => Builders\Registry::get_active_builders(),
            'capabilities' => self::get_user_capabilities(),
            'policies' => self::get_active_policies(),
            'tools' => self::get_available_tools(),
        ];

        return rest_ensure_response($manifest);
    }

    private static function get_user_capabilities() {
        return [
            'can_read_posts' => current_user_can('edit_posts'),
            'can_edit_posts' => current_user_can('edit_posts'),
            'can_create_posts' => current_user_can('edit_posts'),
            'can_publish_posts' => current_user_can('publish_posts'),
            'can_upload_files' => current_user_can('upload_files'),
        ];
    }

    private static function get_active_policies() {
        return [
            'draft_only_mode' => get_option('webweaver_draft_only', 1),
            'allowed_post_types' => get_option('webweaver_allowed_post_types', ['post', 'page']),
            'rate_limit_per_hour' => get_option('webweaver_rate_limit', 60),
            'https_required' => is_ssl(),
        ];
    }

    private static function get_available_tools() {
        $tools = [
            [
                'name' => 'list_posts',
                'description' => 'List posts/pages with filtering',
                'endpoint' => 'GET /wp-json/wp-mcp/v1/posts',
                'parameters' => [
                    'type' => 'string (post|page)',
                    'status' => 'string',
                    'builder' => 'string (gutenberg|elementor|divi)',
                    'search' => 'string',
                    'per_page' => 'integer',
                    'page' => 'integer',
                ],
            ],
            [
                'name' => 'get_post',
                'description' => 'Get post content and metadata',
                'endpoint' => 'GET /wp-json/wp-mcp/v1/post/{id}',
                'parameters' => [
                    'id' => 'integer (post ID)',
                ],
            ],
            [
                'name' => 'create_post',
                'description' => 'Create a new post/page',
                'endpoint' => 'POST /wp-json/wp-mcp/v1/post',
                'parameters' => [
                    'title' => 'string (required)',
                    'type' => 'string (post|page)',
                    'status' => 'string (draft enforced if draft_only_mode)',
                    'builder_mode' => 'string (gutenberg|elementor|divi)',
                    'builder_payload' => 'object (builder-specific)',
                    'meta' => 'object',
                ],
            ],
            [
                'name' => 'update_post',
                'description' => 'Update existing post',
                'endpoint' => 'PUT /wp-json/wp-mcp/v1/post/{id}',
                'parameters' => [
                    'id' => 'integer (post ID)',
                    'title' => 'string',
                    'status' => 'string',
                    'builder_mode' => 'string',
                    'builder_payload' => 'object',
                    'meta' => 'object',
                ],
            ],
            [
                'name' => 'upload_media',
                'description' => 'Upload media file',
                'endpoint' => 'POST /wp-json/wp-mcp/v1/media',
                'parameters' => [
                    'file' => 'file (multipart)',
                ],
            ],
            [
                'name' => 'set_featured_image',
                'description' => 'Set featured image for post',
                'endpoint' => 'PUT /wp-json/wp-mcp/v1/post/{id}/featured-image',
                'parameters' => [
                    'id' => 'integer (post ID)',
                    'media_id' => 'integer (attachment ID)',
                ],
            ],
            [
                'name' => 'activity_log',
                'description' => 'Get activity log (admin only)',
                'endpoint' => 'GET /wp-json/wp-mcp/v1/activity-log',
                'parameters' => [
                    'user_id' => 'integer',
                    'post_id' => 'integer',
                    'action' => 'string',
                    'per_page' => 'integer',
                    'page' => 'integer',
                ],
            ],
        ];

        return $tools;
    }
}
