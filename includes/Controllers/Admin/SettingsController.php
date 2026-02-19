<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
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
        register_setting( 'mtts_lms_options', 'mtts_enable_google_translator' );
    }

    public static function render_settings() {
        $sessions = \MttsLms\Models\Session::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/settings.php';
    }
}
