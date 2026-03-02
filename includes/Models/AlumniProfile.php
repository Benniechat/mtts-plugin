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
            $profile = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ) );
        }

        // Safety: Ensure all expected properties exist to avoid warnings if DB migration lagged
        $defaults = [
            'headline' => '', 'current_ministry' => '', 'location' => '', 'interests' => '',
            'gifts_graces' => '', 'ministry_milestones' => '', 'bio' => '', 'skills' => '',
            'experience' => '', 'profile_picture_url' => '', 'banner_url' => '',
            'graduation_year' => null, 'occupation' => ''
        ];
        foreach ( $defaults as $key => $val ) {
            if ( ! isset( $profile->$key ) ) {
                $profile->$key = $val;
            }
        }

        return $profile;
    }

    public static function update_profile( $user_id, $data ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->update( $table, $data, array( 'user_id' => $user_id ) );
    }
}
