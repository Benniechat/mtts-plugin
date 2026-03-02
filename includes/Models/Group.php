<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Group extends Model {
    protected static $table_name = 'mtts_groups';

    public static function create_group( $data ) {
        return self::create( [
            'name'        => sanitize_text_field( $data['name'] ),
            'description' => sanitize_textarea_field( $data['description'] ),
            'privacy'     => sanitize_key( $data['privacy'] ?? 'public' ),
            'creator_id'  => intval( $data['creator_id'] ),
        ] );
    }

    public static function get_all_groups() {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC" );
    }

    public static function get_group_with_creator( $group_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT g.*, u.display_name as creator_name 
             FROM {$table} g 
             LEFT JOIN {$wpdb->users} u ON g.creator_id = u.ID 
             WHERE g.id = %d",
            $group_id
        ) );
    }
}
