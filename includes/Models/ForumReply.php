<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ForumReply extends Model {
    protected static $table_name = 'mtts_forum_replies';

    /**
     * Get all replies for a given post
     */
    public static function get_by_post( $post_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE post_id = %d ORDER BY created_at ASC",
            $post_id
        ) );
    }
}
