<?php
namespace MttsLms\Controllers;

use MttsLms\Models\Badge;
use MttsLms\Models\Registration;
use MttsLms\Models\Student;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PortfolioController {

    public static function init() {
        add_shortcode( 'mtts_portfolio', array( __CLASS__, 'render_portfolio_shortcode' ) );
    }

    public static function render_portfolio_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'matric' => '',
        ), $atts );

        if ( empty( $atts['matric'] ) ) {
            return '<p>Please specify a matric number.</p>';
        }

        $student = self::get_student_by_matric( $atts['matric'] );
        if ( ! $student ) {
            return '<p>Portfolio not found.</p>';
        }

        return self::render_portfolio_view( $student );
    }

    public static function get_student_by_matric( $matric ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE matric_number = %s", $matric ) );
    }

    public static function render_portfolio_view( $student ) {
        $user = get_userdata( $student->user_id );
        if ( ! $user ) return '';

        $badges = Badge::get_user_badges( $student->user_id );
        $courses = Registration::get_student_courses( $student->id ); // Simplified, get all historically
        
        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Student/portfolio.php';
        return ob_get_clean();
    }
}
