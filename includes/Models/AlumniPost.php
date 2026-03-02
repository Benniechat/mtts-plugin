<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AlumniPost extends Model {
    protected static $table_name = 'mtts_alumni_posts';

    public static function create( $data ) {
        // Enforce character limit for thoughts/nuggets
        if ( isset( $data['type'] ) && $data['type'] === 'nugget' && strlen( $data['content'] ) > 280 ) {
            return false;
        }
        return parent::create( $data );
    }

    public static function get_feed( $limit = 10, $offset = 0 ) {
        global $wpdb;
        $table = self::get_table_name();
        $comments_table = $wpdb->prefix . 'mtts_alumni_comments';
        
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT p.*, u.display_name, 
             (SELECT COUNT(*) FROM {$comments_table} c WHERE c.post_id = p.id) as comments_count 
             FROM {$table} p 
             LEFT JOIN {$wpdb->users} u ON p.author_id = u.ID 
             ORDER BY p.created_at DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ) );
    }

    public static function like_post( $post_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET likes_count = likes_count + 1 WHERE id = %d",
            $post_id
        ) );
    }

    public static function get_posts_by_user( $user_id, $limit = 10 ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE author_id = %d ORDER BY created_at DESC LIMIT %d",
            $user_id, $limit
        ) );
    }

    public static function propagate( $post_id, $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $original = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $post_id ) );
        
        if ( ! $original ) return false;

        $content = "Propagated Word: " . $original->content;
        return self::create( array(
            'author_id' => $user_id,
            'content'   => $content,
            'type'      => 'social'
        ) );
    }
}
