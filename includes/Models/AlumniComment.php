<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AlumniComment extends Model {
    protected static $table_name = 'mtts_alumni_comments';

    public static function get_by_post( $post_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT c.*, u.display_name FROM {$table} c 
             LEFT JOIN {$wpdb->users} u ON c.author_id = u.ID 
             WHERE c.post_id = %d 
             ORDER BY c.created_at ASC",
            $post_id
        ) );
    }

    public static function like_comment( $comment_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET likes_count = COALESCE(likes_count,0) + 1 WHERE id = %d",
            $comment_id
        ) );
    }
}
