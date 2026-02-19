<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Question extends Model {
    protected static $table_name = 'mtts_questions';
    
    public static function get_by_course( $course_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE course_id = %d ORDER BY created_at DESC", $course_id ) );
    }
}
