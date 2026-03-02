<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Lecturer extends Model {
    protected static $table_name = 'mtts_lecturers';

    public static function get_by_user( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ) );
    }

    public static function get_with_user_data() {
        global $wpdb;
        $table = self::get_table_name();
        $users = $wpdb->prefix . 'users';
        return $wpdb->get_results( "SELECT l.*, u.display_name, u.user_email FROM {$table} l JOIN {$users} u ON l.user_id = u.ID" );
    }
}
