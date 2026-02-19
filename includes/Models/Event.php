<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Event extends Model {
    protected static $table_name = 'mtts_events';

    public static function get_by_session( $session_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE session_id = %d ORDER BY start_date ASC", $session_id ) );
    }

    public static function get_upcoming( $limit = 5 ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE start_date >= %s ORDER BY start_date ASC LIMIT %d", current_time( 'Y-m-d' ), $limit ) );
    }
}
