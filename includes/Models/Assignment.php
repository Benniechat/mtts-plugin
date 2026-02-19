<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assignment extends Model {
    protected static $table_name = 'mtts_assignments';
    
    public static function get_by_course( $course_id, $session_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE course_id = %d AND session_id = %d ORDER BY due_date ASC", $course_id, $session_id ) );
    }
}
