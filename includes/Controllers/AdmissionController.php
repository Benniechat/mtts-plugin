<?php
namespace MttsLms\Controllers;

use MttsLms\Models\Program;
use MttsLms\Models\Session;
use MttsLms\Models\Application;
use MttsLms\Models\CampusCenter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdmissionController {

    public static function init() {
        add_shortcode( 'mtts_admission_form', array( __CLASS__, 'render_form' ) );
        add_action( 'init', array( __CLASS__, 'handle_submission' ) );
    }

    public static function render_form() {
        // Get active session
        $session = Session::get_active_session();
        if ( ! $session ) {
            return '<div class="mtts-alert mtts-alert-warning">No active admission session found.</div>';
        }

        $programs       = Program::all();
        $campus_centers = CampusCenter::get_active();
        
        ob_start();
        include MTTS_LMS_PATH . 'templates/admission/form.php';
        return ob_get_clean();
    }

    public static function handle_submission() {
        if ( isset( $_POST['mtts_admission_submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_admission_action' ) ) {
            
            $session = Session::get_active_session();
            if ( ! $session ) {
                wp_die('No active session');
            }

            // Collect & Deep Sanitize Data
            $post_data    = \MttsLms\Core\Security::sanitize_deep( $_POST );
            $basic_fields = ['applicant_name', 'email', 'phone', 'program_id', 'campus_center_id', 'address', 'gender', 'dob', 'denomination'];
            $form_data    = [];
            
            foreach ( $basic_fields as $field ) {
                if ( isset( $post_data[$field] ) ) {
                    $form_data[$field] = $post_data[$field];
                }
            }

            // Handle File Uploads
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            
            $uploaded_files = ['passport', 'credentials'];
            foreach ( $uploaded_files as $file_key ) {
                 if ( ! empty( $_FILES[$file_key]['name'] ) ) {
                    $file = $_FILES[$file_key];
                    $upload_overrides = array( 'test_form' => false );
                    $movefile = wp_handle_upload( $file, $upload_overrides );
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $form_data[$file_key] = $movefile['url'];
                    }
                }
            }

            // Save Application
            $application_data = array(
                'applicant_name'   => $form_data['applicant_name'],
                'email'            => $form_data['email'],
                'phone'            => $form_data['phone'],
                'program_id'       => intval( $form_data['program_id'] ),
                'campus_center_id' => intval( $form_data['campus_center_id'] ?? 0 ),
                'session_id'       => $session->id,
                'form_data'        => json_encode( $form_data ),
                'status'           => 'pending',
                'payment_status'   => 'unpaid'
            );

            $id = Application::create( $application_data );

            if ( $id ) {
                wp_redirect( add_query_arg( 'status', 'success', wp_get_referer() ) );
                exit;
            }
        }
    }

    /**
     * Called when admin approves an application.
     * Generates matric number in format: MTTS/YEAR/CAMPUS_CODE/SERIAL
     *
     * @param object $application  The approved application record
     * @param int    $user_id      The newly created WP user ID
     * @return string              The generated matric number
     */
    public static function generate_matric_for_application( $application, $user_id ) {
        $year              = date( 'Y' );
        $campus_center_id  = intval( $application->campus_center_id ?? 0 );

        if ( $campus_center_id ) {
            $matric = CampusCenter::generate_matric( $campus_center_id, $year );
        } else {
            // Fallback: no campus center assigned — use generic format
            global $wpdb;
            $students_table = $wpdb->prefix . 'mtts_students';
            $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$students_table}" );
            $serial = str_pad( intval( $count ) + 1, 3, '0', STR_PAD_LEFT );
            $matric = "MTTS/{$year}/GEN/{$serial}";
        }

        return $matric;
    }
}
