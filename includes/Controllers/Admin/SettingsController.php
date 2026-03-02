<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'wp_ajax_mtts_send_test_email', array( __CLASS__, 'ajax_send_test_email' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Settings',
            'Settings',
            'manage_options',
            'mtts-settings',
            array( __CLASS__, 'render_settings' )
        );
    }

    public static function register_settings() {
        register_setting( 'mtts_lms_options', 'mtts_current_session_id' );
        register_setting( 'mtts_lms_options', 'mtts_current_semester' );
        register_setting( 'mtts_lms_options', 'mtts_paystack_public_key' );
        register_setting( 'mtts_lms_options', 'mtts_paystack_secret_key' );
        register_setting( 'mtts_lms_options', 'mtts_flutterwave_public_key' );
        register_setting( 'mtts_lms_options', 'mtts_flutterwave_secret_key' );
        register_setting( 'mtts_lms_options', 'mtts_zoom_api_key' );
        register_setting( 'mtts_lms_options', 'mtts_zoom_api_secret' );
        register_setting( 'mtts_lms_options', 'mtts_sms_api_url' );
        register_setting( 'mtts_lms_options', 'mtts_sms_username' );
        register_setting( 'mtts_lms_options', 'mtts_sms_api_key' );
        register_setting( 'mtts_lms_options', 'mtts_sms_sender_id' );
        register_setting( 'mtts_lms_options', 'mtts_stripe_public_key' );
        register_setting( 'mtts_lms_options', 'mtts_stripe_secret_key' );
        register_setting( 'mtts_lms_options', 'mtts_paypal_client_id' );
        register_setting( 'mtts_lms_options', 'mtts_square_app_id' );
        register_setting( 'mtts_lms_options', 'mtts_authorize_login_id' );
        register_setting( 'mtts_lms_options', 'mtts_gemini_api_key' );
        register_setting( 'mtts_lms_options', 'mtts_enable_google_translator' );
        register_setting( 'mtts_lms_options', 'mtts_enable_ai_notifications' );
        register_setting( 'mtts_lms_options', 'mtts_ai_congratulations_template' );
        register_setting( 'mtts_lms_options', 'mtts_matric_format' );
        register_setting( 'mtts_lms_options', 'mtts_reset_link_expiry_hours' );
        register_setting( 'mtts_lms_options', 'mtts_undergraduate_form_price' );
        register_setting( 'mtts_lms_options', 'mtts_postgraduate_form_price' );
        register_setting( 'mtts_lms_options', 'mtts_enable_admission_payments' );
        register_setting( 'mtts_lms_options', 'mtts_active_payment_gateway' );
        register_setting( 'mtts_lms_options', 'mtts_admin_bypass_payments' );

        // SMTP Settings
        register_setting( 'mtts_lms_options', 'mtts_smtp_host' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_port' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_user' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_pass' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_encryption' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_from_email' );
        register_setting( 'mtts_lms_options', 'mtts_smtp_from_name' );
    }

    public static function render_settings() {
        $sessions = \MttsLms\Models\Session::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/settings.php';
    }

    /**
     * AJAX handler for sending test emails
     */
    public static function ajax_send_test_email() {
        check_ajax_referer( 'mtts_lms_settings', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized' ) );
        }

        $to = sanitize_email( $_POST['email'] );
        if ( ! is_email( $to ) ) {
            wp_send_json_error( array( 'message' => 'Invalid email address.' ) );
        }

        $subject = 'MTTS LMS SMTP Test Email';
        $message = 'If you are receiving this email, it means your SMTP settings in MTTS LMS are correctly configured.';

        // Use the process_email_queue worker to test the full delivery path
        \MttsLms\Core\NotificationManager::process_email_queue( $to, $subject, $message );

        wp_send_json_success( array( 'message' => 'Test email sent. Please check your inbox.' ) );
    }
}
