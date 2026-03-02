<?php
namespace MttsLms\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormSession {
    private static $table_name;

    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'mtts_form_sessions';
    }

    public static function get_session( $session_key ) {
        global $wpdb;
        self::init();
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . self::$table_name . " WHERE session_key = %s", $session_key ) );
    }

    public static function save_session( $data ) {
        global $wpdb;
        self::init();
        
        $session_key = $data['session_key'];
        $existing = self::get_session( $session_key );

        if ( $existing ) {
            return $wpdb->update( self::$table_name, $data, array( 'session_key' => $session_key ) );
        } else {
            return $wpdb->insert( self::$table_name, $data );
        }
    }

    public static function delete_session( $session_key ) {
        global $wpdb;
        self::init();
        return $wpdb->delete( self::$table_name, array( 'session_key' => $session_key ) );
    }

    public static function cleanup() {
        global $wpdb;
        self::init();
        // Cleanup sessions older than 30 days
        $wpdb->query( "DELETE FROM " . self::$table_name . " WHERE updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY)" );
    }
}
