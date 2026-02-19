<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Student extends Model {
    protected static $table_name = 'mtts_students';

    /**
     * Get student record by WordPress user ID
     */
    public static function get_by_user( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE user_id = %d",
            $user_id
        ) );
    }

    /**
     * Get student by matric number
     */
    public static function get_by_matric( $matric ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE matric_number = %s",
            $matric
        ) );
    }
}
