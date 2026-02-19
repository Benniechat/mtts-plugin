<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PdfController {

    public static function init() {
        // We will misuse 'pdf' as a generic term for printable documents since adding mpdf/tcpdf requires composer
        add_shortcode( 'mtts_admission_letter', array( __CLASS__, 'render_admission_letter' ) );
        add_action( 'template_redirect', array( __CLASS__, 'handle_request' ) );
    }

    public static function handle_request() {
        if ( isset( $_GET['mtts_doc'] ) ) {
            $doc = sanitize_key( $_GET['mtts_doc'] );

            // Check Access
            if ( ! is_user_logged_in() ) {
                auth_redirect();
            }

            switch ( $doc ) {
                case 'admission_letter':
                    self::render_admission_letter_full();
                    exit;
                    break;
                case 'id_card':
                    self::render_id_card();
                    exit;
                    break;
                case 'transcript':
                    self::render_transcript();
                    exit;
                    break;
                case 'certificate':
                    self::render_certificate();
                    exit;
                    break;
            }
        }
    }

    public static function render_id_card() {
        $student = self::get_current_student();
        $program = \MttsLms\Models\Program::find( $student->program_id );
        $user = get_user_by( 'id', $student->user_id );
        
        include MTTS_LMS_PATH . 'includes/Views/Documents/id-card.php';
    }

    public static function render_transcript() {
        $student = self::get_current_student();
        $program = \MttsLms\Models\Program::find( $student->program_id );
        $user = get_user_by( 'id', $student->user_id );
        
        // Fetch all results
        global $wpdb;
        $results_table = $wpdb->prefix . 'mtts_registrations'; // Using registrations instead of exam_results as it has the final grade/status
        $courses_table = $wpdb->prefix . 'mtts_courses';
        $sessions_table = $wpdb->prefix . 'mtts_sessions';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT r.*, c.course_code, c.course_title, c.credit_unit, s.name as session_name 
            FROM {$results_table} r 
            JOIN {$courses_table} c ON r.course_id = c.id 
            JOIN {$sessions_table} s ON r.session_id = s.id 
            WHERE r.student_id = %d AND r.status IN ('registered', 'approved') 
            ORDER BY r.session_id ASC, r.semester ASC",
            $student->id
        ) );
        
        // Group by Session
        $transcript_data = [];
        foreach($results as $row) {
            $transcript_data[$row->session_name][] = $row;
        }

        include MTTS_LMS_PATH . 'includes/Views/Documents/transcript.php';
    }

    public static function render_certificate() {
        $student = self::get_current_student();
        $program = \MttsLms\Models\Program::find( $student->program_id );
        $user = get_user_by( 'id', $student->user_id );
        
        include MTTS_LMS_PATH . 'includes/Views/Documents/certificate.php';
    }

    private static function get_current_student() {
        $student_id = isset( $_GET['student_id'] ) ? intval( $_GET['student_id'] ) : 0;
        $current_user_id = get_current_user_id();
        global $wpdb;

        if ( current_user_can( 'manage_options' ) && $student_id ) {
            $student = \MttsLms\Models\Student::find( $student_id );
        } else {
            $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mtts_students WHERE user_id = %d", $current_user_id ) );
        }

        if ( ! $student ) {
            wp_die( 'Student record not found.' );
        }
        return $student;
    }

    public static function render_admission_letter_full() {
        $student = self::get_current_student();

        $program = \MttsLms\Models\Program::find( $student->program_id );
        $session = \MttsLms\Models\Session::find( $student->session_id );
        $user = get_user_by( 'id', $student->user_id );

        // Render HTML for Print
        include MTTS_LMS_PATH . 'includes/Views/Documents/admission-letter.php';
    }
}
