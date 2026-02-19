<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Student;
use MttsLms\Core\Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PeopleController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Students',
            'Students',
            'mtts_manage_students',
            'mtts-students',
            array( __CLASS__, 'render_students' )
        );

        add_submenu_page(
            'mtts-lms',
            'Staff',
            'Staff',
            'mtts_manage_staff',
            'mtts-staff',
            array( __CLASS__, 'render_staff' )
        );

        add_submenu_page(
            'mtts-lms',
            'Promotion',
            'Promotion',
            'manage_options',
            'mtts-promotion',
            array( __CLASS__, 'render_promotion' )
        );

        add_submenu_page(
            'mtts-lms',
            'Badges',
            'Badges',
            'manage_options',
            'mtts-badges',
            array( __CLASS__, 'render_badges' )
        );

        add_submenu_page(
            'mtts-lms',
            'Stakeholders',
            'Stakeholders',
            'manage_options',
            'mtts-stakeholders',
            array( __CLASS__, 'render_stakeholders' )
        );
    }

    public static function render_students() {
        // Fetch all students (mtts_students table + users table joined)
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        
        $where = "";
        $current_user = wp_get_current_user();
        if ( in_array( 'mtts_campus_coordinator', (array) $current_user->roles ) ) {
            $campus_id = get_user_meta( $current_user->ID, 'mtts_assigned_campus_id', true );
            if ( $campus_id ) {
                $where = $wpdb->prepare( " WHERE s.campus_center_id = %d", $campus_id );
            } else {
                // If no campus assigned, they see nothing or maybe just a notice?
                // For safety, restrict to none.
                $where = " WHERE 1=0";
            }
        }

        $students = $wpdb->get_results( "SELECT s.*, u.display_name, u.user_email FROM {$table} s LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID $where ORDER BY s.created_at DESC" );
        
        include MTTS_LMS_PATH . 'includes/Views/Admin/students.php';
    }

    public static function render_staff() {
        // Fetch all users with staff roles (lecturer, accountant, registrar, school_admin)
        $args = array(
            'role__in' => array( 'mtts_lecturer', 'mtts_registrar', 'mtts_school_admin', 'mtts_accountant', 'mtts_campus_coordinator' ),
        );

        $current_user = wp_get_current_user();
        if ( in_array( 'mtts_campus_coordinator', (array) $current_user->roles ) ) {
            $campus_id = get_user_meta( $current_user->ID, 'mtts_assigned_campus_id', true );
            if ( $campus_id ) {
                $args['meta_query'] = array(
                    array(
                        'key'     => 'mtts_assigned_campus_id',
                        'value'   => $campus_id,
                        'compare' => '=',
                    ),
                );
            } else {
                $args['include'] = array(0); // Show none
            }
        }

        $staff_query = new \WP_User_Query( $args );
        $staff_members = $staff_query->get_results();

        include MTTS_LMS_PATH . 'includes/Views/Admin/staff.php';
    }

    public static function render_promotion() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/promotion.php';
    }

    public static function render_badges() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/badges.php';
    }

    public static function render_stakeholders() {
        if ( isset( $_POST['mtts_register_stakeholder'] ) && check_admin_referer( 'mtts_register_stakeholder' ) ) {
            self::process_stakeholder_registration();
        }

        // Fetch Alumni & Guests
        $args = array(
            'role__in' => array( 'mtts_alumni', 'mtts_guest' ),
        );
        $stakeholders_query = new \WP_User_Query( $args );
        $stakeholders = $stakeholders_query->get_results();

        include MTTS_LMS_PATH . 'includes/Views/Admin/stakeholders.php';
    }

    private static function process_stakeholder_registration() {
        $email      = sanitize_email( $_POST['email'] );
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name  = sanitize_text_field( $_POST['last_name'] );
        $role       = sanitize_key( $_POST['role'] );

        if ( ! is_email( $email ) ) {
            echo '<div class="notice notice-error"><p>Invalid email address.</p></div>';
            return;
        }

        if ( email_exists( $email ) ) {
            echo '<div class="notice notice-error"><p>User with this email already exists.</p></div>';
            return;
        }

        $username = explode( '@', $email )[0];
        $password = wp_generate_password();

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            echo '<div class="notice notice-error"><p>' . esc_html( $user_id->get_error_message() ) . '</p></div>';
            return;
        }

        // Update user meta
        wp_update_user( [
            'ID'         => $user_id,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'role'       => $role
        ] );

        // Initialize Alumni Profile if needed
        if ( $role === 'mtts_alumni' ) {
            \MttsLms\Models\AlumniProfile::create( [ 'user_id' => $user_id ] );
        }

        echo '<div class="notice notice-success"><p>Stakeholder registered successfully! Password sent to ' . esc_html( $email ) . '</p></div>';
    }
}
