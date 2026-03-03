<?php
/**
 * Media Endpoint
 */

namespace WP_MCP_Connector\API\Endpoints;

use WP_MCP_Connector\Logging;

class Media {
    public static function upload_media(\WP_REST_Request $request) {
        if (!current_user_can('upload_files')) {
            return new \WP_Error('forbidden', 'Cannot upload files', ['status' => 403]);
        }

        // Check if file exists.
        if (!isset($_FILES['file'])) {
            return new \WP_Error('no_file', 'No file provided', ['status' => 400]);
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // Handle upload.
        $attachment_id = media_handle_upload('file', 0);

        if (is_wp_error($attachment_id)) {
            return new \WP_Error('upload_failed', 'Failed to upload file', ['status' => 500]);
        }

        Logging\Activity::log($attachment_id, 'media_upload', 'success', 'media');

        return rest_ensure_response([
            'id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id),
            'title' => get_the_title($attachment_id),
        ]);
    }

    public static function set_featured_image(\WP_REST_Request $request) {
        $post_id = (int)$request->get_param('id');
        $body = $request->get_json_params();
        $media_id = (int)($body['media_id'] ?? 0);

        if (!current_user_can('edit_post', $post_id)) {
            return new \WP_Error('forbidden', 'Cannot edit this post', ['status' => 403]);
        }

        if ($media_id && !wp_attachment_is_image($media_id)) {
            return new \WP_Error('invalid', 'Not an image', ['status' => 400]);
        }

        if ($media_id) {
            set_post_thumbnail($post_id, $media_id);
        } else {
            delete_post_thumbnail($post_id);
        }

        Logging\Activity::log($post_id, 'set_featured_image', 'success', 'media');

        return rest_ensure_response([
            'post_id' => $post_id,
            'media_id' => $media_id,
            'featured_image_url' => get_the_post_thumbnail_url($post_id),
        ]);
    }
}
