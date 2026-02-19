<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BonusMark extends Model {
    protected static $table_name = 'mtts_bonus_marks';

    public static function get_student_bonus( $student_id, $course_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(marks) FROM {$table} WHERE student_id = %d AND course_id = %d", $student_id, $course_id ) );
        return $total ? floatval( $total ) : 0;
    }
}
