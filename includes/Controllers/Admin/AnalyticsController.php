<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AnalyticsController {

    public static function init() {
        // No shortcode needed for admin analytics usually, but we could add one
    }

    public static function get_enrollment_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        return $wpdb->get_results( "SELECT current_level, COUNT(*) as count FROM {$table} GROUP BY current_level" );
    }

    public static function get_grade_distribution( $course_id = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_exam_results';
        $where = $course_id ? $wpdb->prepare( "WHERE course_id = %d", $course_id ) : "";
        
        return $wpdb->get_results( "
            SELECT 
                CASE 
                    WHEN score >= 70 THEN 'A'
                    WHEN score >= 60 THEN 'B'
                    WHEN score >= 50 THEN 'C'
                    WHEN score >= 45 THEN 'D'
                    ELSE 'F'
                END as grade,
                COUNT(*) as count
            FROM {$table}
            {$where}
            GROUP BY grade
            ORDER BY grade ASC
        " );
    }

    public static function get_campus_stats() {
        global $wpdb;
        $students_table = $wpdb->prefix . 'mtts_students';
        $campus_table   = $wpdb->prefix . 'mtts_campus_centers';
        
        return $wpdb->get_results( "
            SELECT c.name, COUNT(s.id) as count 
            FROM {$campus_table} c 
            LEFT JOIN {$students_table} s ON c.id = s.campus_center_id 
            GROUP BY c.id
        " );
    }

    public static function render_dashboard() {
        $enrollment = self::get_enrollment_stats();
        $grades     = self::get_grade_distribution();
        $campus     = self::get_campus_stats();

        include MTTS_LMS_PATH . 'includes/Views/Admin/analytics.php';
    }
}
