<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RoleController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_post_mtts_save_role', array( __CLASS__, 'save_role' ) );
        add_action( 'admin_post_mtts_delete_role', array( __CLASS__, 'delete_role' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Role Manager',
            'Role Manager',
            'manage_options',
            'mtts-roles',
            array( __CLASS__, 'render_roles_page' )
        );
    }

    public static function render_roles_page() {
        global $wp_roles;
        $roles = $wp_roles->roles;
        
        $capability_groups = array(
            'Academic' => array(
                'mtts_manage_courses' => 'Manage Courses',
                'mtts_manage_students' => 'Manage Students',
                'mtts_manage_exams' => 'Manage Exams',
                'mtts_grade_assignments' => 'Grade Assignments',
            ),
            'Financials' => array(
                'mtts_manage_payments' => 'Manage Payments',
                'mtts_view_financial_reports' => 'View Financial Reports',
            ),
            'Management' => array(
                'mtts_manage_staff' => 'Manage Staff',
                'mtts_manage_admissions' => 'Manage Admissions',
                'mtts_view_reports' => 'View System Reports',
            ),
            'Alumni' => array(
                'mtts_access_alumni_network' => 'Access Alumni Network',
                'mtts_manage_alumni_events' => 'Manage Alumni Events',
            )
        );

        include MTTS_LMS_PATH . 'includes/Views/Admin/roles.php';
    }

    public static function save_role() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die('Unauthorized');
        check_admin_referer( 'mtts_save_role' );

        $role_id = sanitize_key( $_POST['role_id'] );
        $role_name = sanitize_text_field( $_POST['role_name'] );
        $capabilities = isset( $_POST['caps'] ) ? (array) $_POST['caps'] : array();

        $caps_to_add = array( 'read' => true );
        foreach ( $capabilities as $cap ) {
            $caps_to_add[ sanitize_key( $cap ) ] = true;
        }

        if ( get_role( $role_id ) ) {
            // Update existing role - WordPress add_role doesn't update caps, so we need a different approach
            $role = get_role( $role_id );
            // Remove old custom caps (simplified for now, ideally track which were added by us)
            // For now, let's just add the new ones
            foreach ( $caps_to_add as $cap => $val ) {
                $role->add_cap( $cap );
            }
        } else {
            add_role( $role_id, $role_name, $caps_to_add );
        }

        wp_redirect( admin_url( 'admin.php?page=mtts-roles&message=saved' ) );
        exit;
    }

    public static function delete_role() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die('Unauthorized');
        $role_id = sanitize_key( $_GET['role'] );
        
        if ( ! in_array( $role_id, array( 'administrator', 'mtts_school_admin', 'mtts_student' ) ) ) {
            remove_role( $role_id );
        }

        wp_redirect( admin_url( 'admin.php?page=mtts-roles&message=deleted' ) );
        exit;
    }
}
