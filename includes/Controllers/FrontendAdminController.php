<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * FrontendAdminController
 * Provides frontend dashboards for non-super-admin staff roles.
 */
class FrontendAdminController {

    public static function init() {
        add_shortcode( 'mtts_school_admin_dashboard', array( __CLASS__, 'render_school_admin' ) );
        add_shortcode( 'mtts_registrar_dashboard',    array( __CLASS__, 'render_registrar' ) );
        add_shortcode( 'mtts_accountant_dashboard',   array( __CLASS__, 'render_accountant' ) );
        add_shortcode( 'mtts_campus_dashboard',       array( __CLASS__, 'render_campus' ) );
        add_action( 'template_redirect', array( __CLASS__, 'check_access' ) );
    }

    public static function check_access() {
        $page_role_map = array(
            'school-admin-dashboard' => 'mtts_school_admin',
            'registrar-dashboard'    => 'mtts_registrar',
            'accountant-dashboard'   => 'mtts_accountant',
            'campus-dashboard'       => 'mtts_campus_coordinator',
        );

        foreach ( $page_role_map as $page_slug => $required_role ) {
            if ( is_page( $page_slug ) ) {
                if ( ! is_user_logged_in() ) {
                    auth_redirect();
                }
                // Allow super admins to also access any admin portal
                if ( ! current_user_can( $required_role ) && ! current_user_can( 'manage_options' ) ) {
                    wp_redirect( home_url() );
                    exit;
                }
            }
        }
    }

    // ==============================
    // SCHOOL ADMIN PORTAL
    // ==============================
    public static function render_school_admin() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $user = wp_get_current_user();
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';
        
        $titles = array(
            'overview' => array('title' => 'School Admin Overview', 'subtitle' => 'Global management of seminary operations.'),
            'students' => array('title' => 'Student Management',   'subtitle' => 'Oversee all registered students across programs.'),
            'lecturers'=> array('title' => 'Faculty Management',   'subtitle' => 'Manage lecturer profiles and course assignments.'),
            'courses'  => array('title' => 'Academic Programs',    'subtitle' => 'Configure degrees, diplomas, and course structures.'),
            'results'  => array('title' => 'Result Verification',  'subtitle' => 'Audit and verify student academic results.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => 'Staff Portal', 'subtitle' => '');
        
        return self::render_portal_shell( 
            $view, 
            'school-admin', 
            $current_title['title'], 
            $current_title['subtitle'] 
        );
    }

    public static function render_registrar() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        $titles = array(
            'overview'   => array('title' => 'Registrar Overview',   'subtitle' => 'Registry and record management portal.'),
            'admissions' => array('title' => 'Admission Processing', 'subtitle' => 'Review and process new student applications.'),
            'students'   => array('title' => 'Student Records',     'subtitle' => 'Maintain official student files and transcripts.'),
            'programs'   => array('title' => 'Academic Registry',   'subtitle' => 'Manage academic sessions and program registration.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => 'Staff Portal', 'subtitle' => '');

        return self::render_portal_shell( 
            $view, 
            'registrar', 
            $current_title['title'], 
            $current_title['subtitle'] 
        );
    }

    public static function render_accountant() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        $titles = array(
            'overview' => array('title' => 'Finance Overview',      'subtitle' => 'Monitor seminary financial status and transactions.'),
            'payments' => array('title' => 'Tuition & Fee Tracking', 'subtitle' => 'Review student payments and payment receipts.'),
            'wallet'   => array('title' => 'Wallet Management',     'subtitle' => 'Manage digital wallet balances and student credits.'),
            'reports'  => array('title' => 'Financial Reporting',   'subtitle' => 'Generate and export financial statements.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => 'Staff Portal', 'subtitle' => '');

        return self::render_portal_shell( 
            $view, 
            'accountant', 
            $current_title['title'], 
            $current_title['subtitle'] 
        );
    }

    public static function render_campus() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        $titles = array(
            'overview' => array('title' => 'Campus Overview',      'subtitle' => 'Manage local campus operations and students.'),
            'students' => array('title' => 'Enrollment List',      'subtitle' => 'View students registered at this campus.'),
            'centers'  => array('title' => 'Center Management',    'subtitle' => 'Coordinate local learning centers.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => 'Staff Portal', 'subtitle' => '');

        return self::render_portal_shell( 
            $view, 
            'campus', 
            $current_title['title'], 
            $current_title['subtitle'] 
        );
    }

    private static function render_portal_shell( $view, $base_slug, $page_title, $page_subtitle = '' ) {
        $user = wp_get_current_user();
        
        // Prepare Sidebar
        $sidebar_path = MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/shared-sidebar.php';

        // Capture Internal View Content
        ob_start();
        $view_file = MTTS_LMS_PATH . "includes/Views/FrontendAdmin/{$base_slug}-{$view}.php";
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            include MTTS_LMS_PATH . "includes/Views/FrontendAdmin/{$base_slug}-overview.php";
        }
        $lms_content = ob_get_clean();

        // Render Scoped Layout
        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Shared/lms-layout.php';
        return ob_get_clean();
    }
}
