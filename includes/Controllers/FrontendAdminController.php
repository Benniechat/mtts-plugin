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
        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/shared-sidebar.php';
        echo '<div class="mtts-dashboard-content">';
        $view_file = MTTS_LMS_PATH . "includes/Views/FrontendAdmin/school-admin-{$view}.php";
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/school-admin-overview.php';
        }
        echo '</div></div>';
        return ob_get_clean();
    }

    // ==============================
    // REGISTRAR PORTAL
    // ==============================
    public static function render_registrar() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $user = wp_get_current_user();
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';
        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/shared-sidebar.php';
        echo '<div class="mtts-dashboard-content">';
        $view_file = MTTS_LMS_PATH . "includes/Views/FrontendAdmin/registrar-{$view}.php";
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/registrar-overview.php';
        }
        echo '</div></div>';
        return ob_get_clean();
    }

    // ==============================
    // ACCOUNTANT PORTAL
    // ==============================
    public static function render_accountant() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $user = wp_get_current_user();
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';
        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/shared-sidebar.php';
        echo '<div class="mtts-dashboard-content">';
        $view_file = MTTS_LMS_PATH . "includes/Views/FrontendAdmin/accountant-{$view}.php";
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/accountant-overview.php';
        }
        echo '</div></div>';
        return ob_get_clean();
    }

    // ==============================
    // CAMPUS COORDINATOR PORTAL
    // ==============================
    public static function render_campus() {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Access Denied. Please log in.</div>';
        }
        $user = wp_get_current_user();
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';
        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/shared-sidebar.php';
        echo '<div class="mtts-dashboard-content">';
        $view_file = MTTS_LMS_PATH . "includes/Views/FrontendAdmin/campus-{$view}.php";
        if ( file_exists( $view_file ) ) {
            include $view_file;
        } else {
            include MTTS_LMS_PATH . 'includes/Views/FrontendAdmin/campus-overview.php';
        }
        echo '</div></div>';
        return ob_get_clean();
    }
}
