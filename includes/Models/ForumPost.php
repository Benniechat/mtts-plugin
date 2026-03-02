<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ForumPost extends Model {
    protected static $table_name = 'mtts_forum_posts';

    /**
     * Get all posts, pinned first, then newest
     */
    public static function get_all( $category = null, $course_id = null ) {
        global $wpdb;
        $table = self::get_table_name();
        $where = 'WHERE is_flagged = 0';
        $args  = [];

        if ( $category ) {
            $where .= ' AND category = %s';
            $args[] = $category;
        }
        if ( $course_id ) {
            $where .= ' AND course_id = %d';
            $args[] = $course_id;
        }
        if ( isset( $category['group_id'] ) ) {
            $where .= ' AND group_id = %d';
            $args[] = intval( $category['group_id'] );
        }

        $sql = "SELECT * FROM {$table} {$where} ORDER BY is_pinned DESC, created_at DESC";
        return $args ? $wpdb->get_results( $wpdb->prepare( $sql, ...$args ) ) : $wpdb->get_results( $sql );
    }

    /**
     * Get reply count for a post
     */
    public static function get_reply_count( $post_id ) {
        global $wpdb;
        $replies_table = $wpdb->prefix . 'mtts_forum_replies';
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$replies_table} WHERE post_id = %d",
            $post_id
        ) );
    }

    /**
     * Pin or unpin a post (admin only)
     */
    public static function toggle_pin( $post_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $post  = self::find( $post_id );
        if ( $post ) {
            $wpdb->update( $table, [ 'is_pinned' => $post->is_pinned ? 0 : 1 ], [ 'id' => $post_id ] );
        }
    }

    /**
     * Flag a post for moderation
     */
    public static function flag( $post_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $wpdb->update( $table, [ 'is_flagged' => 1 ], [ 'id' => $post_id ] );
    }
}
