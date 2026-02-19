<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BulkAdmissionController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_post_mtts_bulk_admission', array( __CLASS__, 'process_upload' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Bulk Admission',
            'Bulk Admission',
            'mtts_manage_admissions',
            'mtts-bulk-admission',
            array( __CLASS__, 'render' )
        );
    }

    public static function render() {
        $programs = \MttsLms\Models\Program::all();
        $sessions = \MttsLms\Models\Session::all();
        $campus_centers = \MttsLms\Models\CampusCenter::all();

        include MTTS_LMS_PATH . 'includes/Views/Admin/bulk-admission.php';
    }

    public static function process_upload() {
        if ( ! current_user_can( 'mtts_manage_admissions' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_bulk_admission' );

        if ( empty( $_FILES['csv_file']['tmp_name'] ) ) {
            wp_redirect( admin_url( 'admin.php?page=mtts-bulk-admission&error=no_file' ) );
            exit;
        }

        $program_id      = intval( $_POST['program_id'] );
        $session_id      = intval( $_POST['session_id'] );
        $campus_id       = intval( $_POST['campus_id'] );
        $default_level   = sanitize_text_field( $_POST['level'] );

        $handle = fopen( $_FILES['csv_file']['tmp_name'], 'r' );
        $header = fgetcsv( $handle ); // Skip header

        $imported = 0;
        $errors = [];

        while ( ( $row = fgetcsv( $handle ) ) !== false ) {
            if ( count( $row ) < 2 ) continue;

            $name  = sanitize_text_field( $row[0] );
            $email = sanitize_email( $row[1] );
            $phone = isset( $row[2] ) ? sanitize_text_field( $row[2] ) : '';

            if ( ! is_email( $email ) ) {
                $errors[] = "Invalid email for {$name}: {$email}";
                continue;
            }

            if ( email_exists( $email ) ) {
                $errors[] = "Email already exists for {$name}: {$email}";
                continue;
            }

            // Create Application Record (to trigger logic or bypass to student)
            // For bulk, let's create student record DIRCTLY and skip application if requested,
            // but usually it's better to create an approved application for audit.
            
            global $wpdb;
            $wpdb->insert( $wpdb->prefix . 'mtts_applications', array(
                'applicant_name' => $name,
                'email'          => $email,
                'phone'          => $phone,
                'program_id'     => $program_id,
                'session_id'     => $session_id,
                'form_data'      => json_encode( [ 'manual_bulk' => true ] ),
                'status'         => 'pending',
                'payment_status' => 'paid'
            ) );

            $application_id = $wpdb->insert_id;

            // Immediately approve
            $result = \MttsLms\Core\AdmissionProcessor::approve_application( $application_id );
            
            if ( $result === true ) {
                $imported++;
                // Set campus ID (AdmissionProcessor doesn't handle campus yet)
                $user_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}mtts_students WHERE user_id = (SELECT ID FROM {$wpdb->users} WHERE user_email = %s)", $email ) );
                if ( $user_id ) {
                    $wpdb->update( $wpdb->prefix . 'mtts_students', [ 'campus_center_id' => $campus_id ], [ 'user_id' => $user_id ] );
                }
            } else {
                $errors[] = "Failed to approve {$name}: " . ( is_wp_error($result) ? $result->get_error_message() : 'Unknown error' );
            }
        }

        fclose( $handle );

        set_transient( 'mtts_bulk_errors', $errors, 60 );
        wp_redirect( admin_url( 'admin.php?page=mtts-bulk-admission&imported=' . $imported ) );
        exit;
    }
}
