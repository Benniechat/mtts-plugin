<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Transaction extends Model {
    protected static $table_name = 'mtts_transactions';
    
    public static function get_student_transactions( $student_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE student_id = %d ORDER BY created_at DESC", $student_id ) );
    }
}
