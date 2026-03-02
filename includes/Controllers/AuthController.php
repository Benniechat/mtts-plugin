<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthController {

    public static function init() {
        add_shortcode( 'mtts_login_form', array( __CLASS__, 'render_login_form' ) );
        add_action( 'init', array( __CLASS__, 'handle_login' ) );
        add_action( 'wp_logout', array( __CLASS__, 'redirect_after_logout' ) );
    }

    public static function render_login_form( $atts ) {
        if ( is_user_logged_in() ) {
            return '<p>You are already logged in.</p>';
        }

        $args = shortcode_atts( array(
            'redirect' => home_url(),
        ), $atts );

        ob_start();
        include MTTS_LMS_PATH . 'templates/auth/login.php';
        return ob_get_clean();
    }

    public static function handle_login() {
        if ( isset( $_POST['mtts_login_submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_login_action' ) ) {
            $creds = array(
                'user_login'    => sanitize_text_field( $_POST['mtts_username'] ),
                'user_password' => $_POST['mtts_password'],
                'remember'      => isset( $_POST['mtts_remember'] ),
            );

            $user = wp_signon( $creds, is_ssl() );

            if ( is_wp_error( $user ) ) {
                // Store error in session or query arg to display on form
                // For now, simple redirect back with error
                wp_redirect( add_query_arg( 'login_error', 'invalid_credentials', wp_get_referer() ) );
                exit;
            }

            // Redirect based on role
            $redirect_url = self::get_redirect_url( $user );
            wp_redirect( $redirect_url );
            exit;
        }
    }

    public static function redirect_after_logout() {
        wp_redirect( home_url() ); // Or custom login page
        exit;
    }

    private static function get_redirect_url( $user ) {
        $roles = (array) $user->roles;
        $mtts_roles = array( 'mtts_student', 'mtts_lecturer', 'mtts_school_admin', 'mtts_accountant', 'mtts_registrar', 'mtts_campus_coordinator' );
        $user_mtts_roles = array_intersect( $mtts_roles, $roles );

        // 1. Multiple Roles -> Switcher
        if ( count( $user_mtts_roles ) > 1 ) {
            return home_url( '/dashboard-switcher' );
        }

        // 2. Categorical Redirection
        if ( in_array( 'mtts_student', $roles ) ) {
            return home_url( '/student-dashboard' );
        } 
        
        if ( in_array( 'mtts_lecturer', $roles ) ) {
            return home_url( '/lecturer-dashboard' );
        } 

        // Administrative Staff — each gets a dedicated frontend dashboard
        if ( in_array( 'mtts_school_admin', $roles ) ) {
            return home_url( '/school-admin-dashboard' );
        }
        if ( in_array( 'mtts_registrar', $roles ) ) {
            return home_url( '/registrar-dashboard' );
        }
        if ( in_array( 'mtts_accountant', $roles ) ) {
            return home_url( '/accountant-dashboard' );
        }
        if ( in_array( 'mtts_campus_coordinator', $roles ) ) {
            return home_url( '/campus-dashboard' );
        }
        // Only the WordPress super-admin goes to wp-admin
        if ( in_array( 'administrator', $roles ) ) {
            return admin_url();
        }

        // 3. Fallback: Alumni & Community Network
        return home_url( '/alumni-network' );
    }
}
