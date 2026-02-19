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
        if ( in_array( 'mtts_student', (array) $user->roles ) ) {
            return home_url( '/student-dashboard' ); // Adjust as per real page
        } elseif ( in_array( 'mtts_lecturer', (array) $user->roles ) ) {
            return home_url( '/lecturer-dashboard' );
        } elseif ( in_array( 'mtts_school_admin', (array) $user->roles ) || in_array( 'administrator', (array) $user->roles ) ) {
            return admin_url();
        }
        return home_url();
    }
}
