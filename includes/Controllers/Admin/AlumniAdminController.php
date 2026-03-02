<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AlumniAdminController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_action( 'admin_post_mtts_save_alumni', array( __CLASS__, 'save_alumni' ) );
    }

    public static function register_settings() {
        register_setting( 'mtts_alumni_settings', 'mtts_alumni_auto_join' );
        register_setting( 'mtts_alumni_settings', 'mtts_alumni_moderation' );
        register_setting( 'mtts_alumni_settings', 'mtts_alumni_visibility' );
        register_setting( 'mtts_alumni_settings', 'mtts_alumni_bbpress_sync' );
        register_setting( 'mtts_alumni_settings', 'mtts_alumni_peepso_sync' );
    }

    public static function register_menus() {
        add_menu_page(
            'MTTS Alumni',
            'MTTS Alumni',
            'manage_options',
            'mtts-alumni',
            array( __CLASS__, 'render_dashboard' ),
            'dashicons-groups',
            26
        );

        add_submenu_page(
            'mtts-alumni',
            'Alumni Dashboard',
            'Dashboard',
            'manage_options',
            'mtts-alumni',
            array( __CLASS__, 'render_dashboard' )
        );

        add_submenu_page(
            'mtts-alumni',
            'Alumni Directory',
            'Directory',
            'manage_options',
            'mtts-alumni-directory',
            array( __CLASS__, 'render_directory' )
        );

        add_submenu_page(
            'mtts-alumni',
            'Social Feed Moderation',
            'Social Feed',
            'manage_options',
            'mtts-alumni-social',
            array( __CLASS__, 'render_social_feed' )
        );

        add_submenu_page(
            'mtts-alumni',
            'Alumni Settings',
            'Settings',
            'manage_options',
            'mtts-alumni-settings',
            array( __CLASS__, 'render_settings' )
        );
    }

    public static function render_dashboard() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/alumni-dashboard.php';
    }

    public static function render_directory() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/alumni-directory.php';
    }

    public static function render_social_feed() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/alumni-social.php';
    }

    public static function render_settings() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/alumni-settings.php';
    }

    public static function save_alumni() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_save_alumni' );

        $user_id = intval( $_POST['user_id'] );
        $student_id = intval( $_POST['student_id'] );

        // Update User Data
        wp_update_user( array(
            'ID'           => $user_id,
            'display_name' => sanitize_text_field( $_POST['display_name'] ),
            'user_email'   => sanitize_email( $_POST['user_email'] )
        ) );

        // Update Student/Alumni Record (Levels etc)
        if ( $student_id ) {
            \MttsLms\Models\Student::update( $student_id, array(
                'current_level' => intval( $_POST['current_level'] ),
                'status'        => 'graduated' // Ensure they stay as graduated
            ) );
        }

        // Update Alumni Profile specific fields
        $alumni_profile = \MttsLms\Models\AlumniProfile::get_by_user( $user_id );
        if ( $alumni_profile ) {
            \MttsLms\Models\AlumniProfile::update( $alumni_profile->id, array(
                'bio'              => sanitize_textarea_field( $_POST['bio'] ),
                'graduation_year'  => intval( $_POST['graduation_year'] ),
                'occupation'       => sanitize_text_field( $_POST['occupation'] ),
                'current_ministry' => sanitize_text_field( $_POST['occupation'] ) // Sync for compatibility
            ) );
        }

        wp_redirect( admin_url( 'admin.php?page=mtts-alumni-directory&message=updated' ) );
        exit;
    }
}
