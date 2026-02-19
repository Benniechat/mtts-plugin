<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Course extends Model {
    protected static $table_name = 'mtts_courses';

    public static function get_by_program_and_level( $program_id, $level, $semester = null ) {
        global $wpdb;
        $table = self::get_table_name();
        $query = "SELECT * FROM {$table} WHERE program_id = %d AND level = %s";
        $args = array( $program_id, $level );

        if ( $semester ) {
            $query .= " AND semester = %s";
            $args[] = $semester;
        }

        return $wpdb->get_results( $wpdb->prepare( $query, $args ) );
    }
}
