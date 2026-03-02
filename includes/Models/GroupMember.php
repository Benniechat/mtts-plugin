<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GroupMember extends Model {
    protected static $table_name = 'mtts_group_members';

    public static function add_member( $group_id, $user_id, $role = 'member' ) {
        if ( self::is_member( $group_id, $user_id ) ) {
            return false;
        }
        return self::create( [
            'group_id' => intval( $group_id ),
            'user_id'  => intval( $user_id ),
            'role'     => sanitize_key( $role ),
        ] );
    }

    public static function is_member( $group_id, $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE group_id = %d AND user_id = %d",
            $group_id, $user_id
        ) );
    }

    public static function get_group_members( $group_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT gm.*, u.display_name, u.user_email 
             FROM {$table} gm 
             LEFT JOIN {$wpdb->users} u ON gm.user_id = u.ID 
             WHERE gm.group_id = %d ORDER BY gm.role ASC, u.display_name ASC",
            $group_id
        ) );
    }

    public static function remove_member( $group_id, $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->delete( $table, [ 'group_id' => $group_id, 'user_id' => $user_id ] );
    }
}
