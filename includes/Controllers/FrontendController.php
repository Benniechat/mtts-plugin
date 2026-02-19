<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FrontendController {

    public static function init() {
        add_shortcode( 'mtts_course_catalog', array( __CLASS__, 'render_catalog' ) );
    }

    public static function render_catalog( $atts ) {
        // Enforce frontend styles just in case
        wp_enqueue_style( 'mtts-lms-css' );
        
        $programs = \MttsLms\Models\Program::all();
        
        // Group courses by program for display
        $catalog = [];
        foreach ( $programs as $program ) {
            $courses = \MttsLms\Models\Course::get_by_program_and_level( $program->id, null ); // fetch all for program
            $catalog[$program->name] = $courses;
        }

        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Frontend/catalog.php';
        return ob_get_clean();
    }
}
