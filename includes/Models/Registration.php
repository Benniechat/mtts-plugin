<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registration extends Model {
    protected static $table_name = 'mtts_registrations';
    
    public static function get_student_courses( $student_id, $session_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( 
            "SELECT * FROM {$table} WHERE student_id = %d AND session_id = %d", 
            $student_id, 
            $session_id 
        ) );
    }
}
