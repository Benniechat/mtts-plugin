<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AlumniProfile extends Model {
    protected static $table_name = 'mtts_alumni_profiles';

    public static function get_by_user( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        $profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ) );

        if ( ! $profile ) {
            // Auto-create profile if not exists
            $wpdb->insert( $table, array( 'user_id' => $user_id ) );
            return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ) );
        }

        return $profile;
    }

    public static function update_profile( $user_id, $data ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->update( $table, $data, array( 'user_id' => $user_id ) );
    }
}
