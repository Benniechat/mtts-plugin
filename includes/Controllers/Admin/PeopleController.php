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
        add_action( 'admin_post_mtts_save_student', array( __CLASS__, 'save_student' ) );
        add_action( 'admin_post_mtts_delete_user', array( __CLASS__, 'delete_user_securely' ) );
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
                $where = " WHERE 1=0";
            }
        }

        $students = $wpdb->get_results( "SELECT s.*, u.display_name, u.user_email FROM {$table} s LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID $where ORDER BY s.created_at DESC" );
        
        $programs = \MttsLms\Models\Program::all();
        $campuses = \MttsLms\Models\CampusCenter::all();
        
        include MTTS_LMS_PATH . 'includes/Views/Admin/students.php';
    }

    public static function save_student() {
        if ( ! current_user_can( 'mtts_manage_students' ) && ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_save_student' );

        $id = intval( $_POST['id'] );
        $data = array(
            'current_level'    => intval( $_POST['current_level'] ),
            'program_id'       => intval( $_POST['program_id'] ),
            'campus_center_id' => intval( $_POST['campus_center_id'] ),
            'status'           => sanitize_text_field( $_POST['status'] ),
        );

        if ( $id ) {
            \MttsLms\Models\Student::update( $id, $data );
            $message = 'updated';
        }

        wp_redirect( admin_url( 'admin.php?page=mtts-students&message=' . $message ) );
        exit;
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

        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
            self::delete_stakeholder( intval( $_GET['id'] ) );
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
        $user_id    = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $email      = sanitize_email( $_POST['email'] );
        $first_name = sanitize_text_field( $_POST['first_name'] );
        $last_name  = sanitize_text_field( $_POST['last_name'] );
        $role       = sanitize_key( $_POST['role'] );

        if ( ! is_email( $email ) ) {
            echo '<div class="notice notice-error"><p>Invalid email address.</p></div>';
            return;
        }

        if ( ! $user_id && email_exists( $email ) ) {
            echo '<div class="notice notice-error"><p>User with this email already exists.</p></div>';
            return;
        }

        if ( $user_id ) {
            // Update existing user
            wp_update_user( array(
                'ID'         => $user_id,
                'user_email' => $email,
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'role'       => $role
            ) );
            echo '<div class="notice notice-success"><p>Stakeholder updated successfully!</p></div>';
        } else {
            // Create new user
            $username = explode( '@', $email )[0];
            $password = wp_generate_password();

            $user_id = wp_create_user( $username, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html( $user_id->get_error_message() ) . '</p></div>';
                return;
            }

            wp_update_user( array(
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
                'role'       => $role
            ) );

            if ( $role === 'mtts_alumni' ) {
                \MttsLms\Models\AlumniProfile::create( array( 'user_id' => $user_id ) );
            }

            // Send AI Welcome Notification
            \MttsLms\Core\NotificationManager::send_welcome_email( get_userdata( $user_id ), $password );

            echo '<div class="notice notice-success"><p>Stakeholder registered successfully! Credentials sent to ' . esc_html( $email ) . '</p></div>';
        }
    }

    private static function delete_stakeholder( $id ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        check_admin_referer( 'mtts_delete_stakeholder_' . $id );

        // Basic safety: only delete if it has the right role
        $user = get_userdata( $id );
        if ( $user && ( in_array( 'mtts_alumni', $user->roles ) || in_array( 'mtts_guest', $user->roles ) ) ) {
            wp_delete_user( $id );
            wp_redirect( admin_url( 'admin.php?page=mtts-stakeholders&message=deleted' ) );
            exit;
        }
    }

    public static function delete_user_securely() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die('Unauthorized');
        
        $user_id = intval( $_GET['id'] );
        check_admin_referer( 'mtts_delete_user_' . $user_id );

        // 1. Safety Checks
        $current_user_id = get_current_user_id();
        if ( $user_id === $current_user_id ) {
            wp_die('You cannot delete yourself.');
        }

        $user_to_delete = get_userdata( $user_id );
        if ( ! $user_to_delete || in_array( 'administrator', (array) $user_to_delete->roles ) ) {
             wp_die('Super Admins cannot be deleted via this portal.');
        }

        // 2. Cascading Cleanup of MTTS Records
        global $wpdb;
        // Delete student record if exists
        $wpdb->delete( $wpdb->prefix . 'mtts_students', array( 'user_id' => $user_id ) );
        // Delete lecturer record if exists
        $wpdb->delete( $wpdb->prefix . 'mtts_lecturers', array( 'user_id' => $user_id ) );
        // Delete wallet if exists
        $wpdb->delete( $wpdb->prefix . 'mtts_wallets', array( 'student_id' => $user_id ) ); 
        
        // 3. Delete WP User
        wp_delete_user( $user_id );

        $referer = wp_get_referer();
        wp_redirect( add_query_arg( 'message', 'user_deleted', $referer ) );
        exit;
    }
}
