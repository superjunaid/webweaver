<?php
/**
 * REST API Routes
 */

namespace WP_MCP_Connector\API;

class Routes {
    public static function register_routes() {
        $namespace = 'wp-mcp/v1';

        // Tools manifest.
        register_rest_route($namespace, '/tools', [
            'methods' => 'GET',
            'callback' => [Endpoints\Tools::class, 'get_manifest'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // List posts.
        register_rest_route($namespace, '/posts', [
            'methods' => 'GET',
            'callback' => [Endpoints\Posts::class, 'list_posts'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
            'args' => self::list_args(),
        ]);

        // Get post.
        register_rest_route($namespace, '/post/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [Endpoints\Posts::class, 'get_post'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // Create post.
        register_rest_route($namespace, '/post', [
            'methods' => 'POST',
            'callback' => [Endpoints\Posts::class, 'create_post'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // Update post.
        register_rest_route($namespace, '/post/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [Endpoints\Posts::class, 'update_post'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // Media upload.
        register_rest_route($namespace, '/media', [
            'methods' => 'POST',
            'callback' => [Endpoints\Media::class, 'upload_media'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // Set featured image.
        register_rest_route($namespace, '/post/(?P<id>\d+)/featured-image', [
            'methods' => 'PUT',
            'callback' => [Endpoints\Media::class, 'set_featured_image'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth'],
        ]);

        // Activity log.
        register_rest_route($namespace, '/activity-log', [
            'methods' => 'GET',
            'callback' => [Endpoints\ActivityLog::class, 'get_log'],
            'permission_callback' => [Auth\Authenticate::class, 'check_auth_admin'],
        ]);
    }

    private static function list_args() {
        return [
            'type' => [
                'type' => 'string',
                'default' => 'post',
                'enum' => ['post', 'page'],
            ],
            'status' => [
                'type' => 'string',
                'default' => 'any',
            ],
            'builder' => [
                'type' => 'string',
                'enum' => ['gutenberg', 'elementor', 'divi', ''],
            ],
            'search' => [
                'type' => 'string',
            ],
            'per_page' => [
                'type' => 'integer',
                'default' => 20,
            ],
            'page' => [
                'type' => 'integer',
                'default' => 1,
            ],
        ];
    }
}
