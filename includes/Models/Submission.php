<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Submission extends Model {
    protected static $table_name = 'mtts_submissions';
    
    public static function get_by_assignment( $assignment_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE assignment_id = %d ORDER BY submitted_at ASC", $assignment_id ) );
    }
}
