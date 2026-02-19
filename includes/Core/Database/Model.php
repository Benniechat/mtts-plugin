<?php
namespace MttsLms\Core\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Model {

    protected static $table_name;
    protected static $primary_key = 'id';

    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . static::$table_name;
    }

    public static function all() {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC" );
    }

    public static function find( $id ) {
        global $wpdb;
        $table = self::get_table_name();
        $pk = static::$primary_key;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$pk} = %d", $id ) );
    }

    public static function create( $data ) {
        global $wpdb;
        $table = self::get_table_name();
        $format = self::get_format( $data );
        
        $result = $wpdb->insert( $table, $data, $format );
        
        if ( $result ) {
            return $wpdb->insert_id;
        }
        return false;
    }

    public static function update( $id, $data ) {
        global $wpdb;
        $table = self::get_table_name();
        $pk = static::$primary_key;
        $format = self::get_format( $data );
        
        return $wpdb->update( $table, $data, array( $pk => $id ), $format, array( '%d' ) );
    }

    public static function delete( $id ) {
        global $wpdb;
        $table = self::get_table_name();
        $pk = static::$primary_key;
        return $wpdb->delete( $table, array( $pk => $id ), array( '%d' ) );
    }
    
    public static function where( $column, $value ) {
        global $wpdb;
        $table    = self::get_table_name();
        $column   = preg_replace( '/[^a-zA-Z0-9_]/', '', $column ); // Sanitize column name
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} = %s", $value ) );
    }

    private static function get_format( $data ) {
        $format = array();
        foreach ( $data as $value ) {
            if ( is_int( $value ) ) {
                $format[] = '%d';
            } elseif ( is_float( $value ) ) {
                $format[] = '%f';
            } else {
                $format[] = '%s';
            }
        }
        return $format;
    }
}
