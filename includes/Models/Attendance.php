<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Attendance extends Model {
    protected static $table_name = 'mtts_attendance';
    
    public static function get_by_course_and_date( $course_id, $date ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE course_id = %d AND class_date = %s", $course_id, $date ) );
    }
}
