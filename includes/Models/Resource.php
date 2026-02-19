<?php
namespace MttsLms\Models;

use MttsLms\Core\Database\Model;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Resource extends Model {
    protected static $table_name = 'mtts_resources';

    /**
     * Get all resources, optionally filtered by course
     */
    public static function get_all( $course_id = null ) {
        global $wpdb;
        $table = self::get_table_name();
        if ( $course_id ) {
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table} WHERE course_id = %d OR course_id IS NULL ORDER BY created_at DESC",
                $course_id
            ) );
        }
        return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC" );
    }

    /**
     * Get resources for a specific lecturer
     */
    public static function get_by_lecturer( $user_id ) {
        global $wpdb;
        $table = self::get_table_name();
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE uploaded_by = %d ORDER BY created_at DESC",
            $user_id
        ) );
    }

    /**
     * Get resources for courses a student is enrolled in
     */
    public static function get_for_student( $student_id, $session_id ) {
        global $wpdb;
        $table      = self::get_table_name();
        $reg_table  = $wpdb->prefix . 'mtts_registrations';

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT r.* FROM {$table} r
             LEFT JOIN {$reg_table} reg ON r.course_id = reg.course_id
             WHERE (reg.student_id = %d AND reg.session_id = %d) OR r.course_id IS NULL
             GROUP BY r.id
             ORDER BY r.created_at DESC",
            $student_id, $session_id
        ) );
    }

    /**
     * Get icon class based on resource type
     */
    public static function get_icon( $type ) {
        $icons = [
            'pdf'   => '📄',
            'video' => '🎬',
            'ebook' => '📖',
            'audio' => '🎵',
            'link'  => '🔗',
            'other' => '📁',
        ];
        return $icons[ $type ] ?? '📁';
    }
}
