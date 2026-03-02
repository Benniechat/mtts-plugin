<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Application;
use MttsLms\Models\Program;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdmissionAdminController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_post_mtts_process_application', array( __CLASS__, 'process_application' ) );
        add_action( 'admin_post_mtts_manual_legacy_admission', array( __CLASS__, 'process_legacy_admission' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Applications',
            'Applications',
            'mtts_manage_admissions', // Capability check
            'mtts-applications',
            array( __CLASS__, 'render_applications' )
        );

        add_submenu_page(
            'mtts-lms',
            'Legacy Onboarding',
            'Legacy Onboarding',
            'mtts_manage_admissions',
            'mtts-legacy-admission',
            array( __CLASS__, 'render_legacy_admission' )
        );
    }

    public static function render_applications() {
        if ( isset( $_GET['view'] ) && $_GET['view'] == 'details' && isset( $_GET['id'] ) ) {
            self::render_application_details( intval( $_GET['id'] ) );
        } else {
            self::render_application_list();
        }
    }

    public static function render_legacy_admission() {
        $programs = Program::all();
        $campuses = \MttsLms\Models\CampusCenter::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/legacy-admission.php';
    }

    private static function render_application_list() {
        // Simple pagination could be added here
        $applications = Application::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/applications-list.php';
    }

    private static function render_application_details( $id ) {
        $application = Application::find( $id );
        if ( ! $application ) {
            echo '<div class="notice notice-error"><p>Application not found.</p></div>';
            self::render_application_list();
            return;
        }
        
        $program = Program::find( $application->program_id );
        $session = Session::find( $application->session_id );
        $form_data = json_decode( $application->form_data, true );

        include MTTS_LMS_PATH . 'includes/Views/Admin/application-details.php';
    }

    public static function process_application() {
        if ( ! current_user_can( 'mtts_manage_admissions' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_process_application' );

        $application_id = intval( $_POST['application_id'] );
        $action = sanitize_text_field( $_POST['mtts_action'] ); // approve or reject

        if ( $action === 'approve' ) {
            // Trigger approval workflow
            \MttsLms\Core\AdmissionProcessor::approve_application( $application_id );
            $message = 'approved';
        } elseif ( $action === 'reject' ) {
            Application::update( $application_id, array( 'status' => 'rejected' ) );
            $message = 'rejected';
        }

        wp_redirect( admin_url( 'admin.php?page=mtts-applications&message=' . $message ) );
        exit;
    }

    public static function process_legacy_admission() {
        if ( ! current_user_can( 'mtts_manage_admissions' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_manual_legacy_admission' );

        $email = sanitize_email( $_POST['email'] );
        $full_name = sanitize_text_field( $_POST['full_name'] );
        $program_id = intval( $_POST['program_id'] );
        $campus_id = intval( $_POST['campus_id'] );
        $admission_year = intval( $_POST['admission_year'] );
        $current_level = sanitize_text_field( $_POST['current_level'] );
        $current_gpa = floatval( $_POST['current_gpa'] );
        $cumulative_gpa = floatval( $_POST['cumulative_gpa'] );

        // 1. Create WP User
        $username = explode( '@', $email )[0];
        $password = wp_generate_password();
        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            wp_die( $user_id->get_error_message() );
        }

        $parts = explode( ' ', $full_name );
        wp_update_user( array(
            'ID' => $user_id,
            'first_name' => $parts[0],
            'last_name' => isset( $parts[1] ) ? $parts[1] : '',
            'role' => 'mtts_student'
        ) );

        // 2. Generate Backdated Matric
        $campus = \MttsLms\Models\CampusCenter::find( $campus_id );
        $matric = \MttsLms\Helpers\MatricNumberHelper::generate( $campus->name, $admission_year );

        // 3. Create Student Profile
        \MttsLms\Models\Student::create( array(
            'user_id' => $user_id,
            'matric_number' => $matric,
            'program_id' => $program_id,
            'campus_center_id' => $campus_id,
            'current_level' => $current_level,
            'admission_year' => $admission_year,
            'current_gpa' => $current_gpa,
            'cumulative_gpa' => $cumulative_gpa,
            'status' => 'active'
        ) );

        // 4. Send AI Welcome Notification
        \MttsLms\Core\NotificationManager::send_welcome_email( get_userdata( $user_id ), $password );

        wp_redirect( admin_url( 'admin.php?page=mtts-students&message=legay_onboarded' ) );
        exit;
    }
}
