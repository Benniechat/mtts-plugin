<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Session extends Model {
    protected static $table_name = 'mtts_sessions';
    
    public static function get_active_session() {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_row( "SELECT * FROM {$table} WHERE status = 'active' LIMIT 1" );
    }
}
