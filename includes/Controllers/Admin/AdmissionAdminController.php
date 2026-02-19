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
    }

    public static function render_applications() {
        if ( isset( $_GET['view'] ) && $_GET['view'] == 'details' && isset( $_GET['id'] ) ) {
            self::render_application_details( intval( $_GET['id'] ) );
        } else {
            self::render_application_list();
        }
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
}
