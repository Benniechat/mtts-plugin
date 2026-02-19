<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Program;
use MttsLms\Models\Session;
use MttsLms\Models\Course;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AcademicController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_post_mtts_save_program', array( __CLASS__, 'save_program' ) );
        add_action( 'admin_post_mtts_save_session', array( __CLASS__, 'save_session' ) );
        add_action( 'admin_post_mtts_save_course', array( __CLASS__, 'save_course' ) );
        add_action( 'admin_post_mtts_save_campus_center', array( __CLASS__, 'save_campus_center' ) );
    }

    public static function register_menus() {
        add_menu_page(
            'MTTS LMS',
            'MTTS LMS',
            'manage_options',
            'mtts-lms',
            array( __CLASS__, 'render_dashboard' ),
            'dashicons-welcome-learn-more',
            6
        );

        add_submenu_page(
            'mtts-lms',
            'Programs',
            'Programs',
            'manage_options',
            'mtts-programs',
            array( __CLASS__, 'render_programs' )
        );

        add_submenu_page(
            'mtts-lms',
            'Settings',
            'Settings',
            'manage_options',
            'mtts-settings',
            array( __CLASS__, 'render_settings' )
        );

        add_submenu_page(
            'mtts-lms',
            'Form Builder',
            'Form Builder',
            'manage_options',
            'mtts-form-builder',
            array( __CLASS__, 'render_form_builder' )
        );

        add_submenu_page(
            'mtts-lms',
            'Sessions',
            'Sessions',
            'manage_options',
            'mtts-sessions',
            array( __CLASS__, 'render_sessions' )
        );

        add_submenu_page(
            'mtts-lms',
            'Courses',
            'Courses',
            'manage_options',
            'mtts-courses',
            array( __CLASS__, 'render_courses' )
        );

        add_submenu_page(
            'mtts-lms',
            'Calendar',
            'Calendar',
            'manage_options',
            'mtts-calendar',
            array( __CLASS__, 'calendar' )
        );

        add_submenu_page(
            'mtts-lms',
            'Campus Centers',
            'Campus Centers',
            'manage_options',
            'mtts-campus-centers',
            array( __CLASS__, 'render_campus_centers' )
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

    public static function render_dashboard() {
        echo '<h1>MTTS LMS Dashboard</h1>';
    }

    public static function render_programs() {
        $programs = Program::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/programs.php';
    }

    public static function render_sessions() {
        $sessions = Session::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/sessions.php';
    }

    public static function render_courses() {
        $courses = Course::all();
        $programs = Program::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/courses.php';
    }

    public static function save_program() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_save_program' );

        $data = array(
            'name' => sanitize_text_field( $_POST['name'] ),
            'code' => sanitize_text_field( $_POST['code'] ),
            'duration_years' => intval( $_POST['duration_years'] ),
            'levels' => intval( $_POST['levels'] ),
            'certificate_type' => sanitize_text_field( $_POST['certificate_type'] ),
        );

        Program::create( $data );

        wp_redirect( admin_url( 'admin.php?page=mtts-programs&message=saved' ) );
        exit;
    }

    public static function save_session() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_save_session' );

        $data = array(
            'name' => sanitize_text_field( $_POST['name'] ),
            'start_date' => sanitize_text_field( $_POST['start_date'] ),
            'end_date' => sanitize_text_field( $_POST['end_date'] ),
            'status' => sanitize_text_field( $_POST['status'] ),
        );

        Session::create( $data );

        wp_redirect( admin_url( 'admin.php?page=mtts-sessions&message=saved' ) );
        exit;
    }

    public static function save_course() {
        if ( ! current_user_can( 'manage_options' ) ) {
             wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_save_course' );

        $data = array(
            'course_code' => sanitize_text_field( $_POST['course_code'] ),
            'course_title' => sanitize_text_field( $_POST['course_title'] ),
            'credit_unit' => intval( $_POST['credit_unit'] ),
            'program_id' => intval( $_POST['program_id'] ),
            'level' => sanitize_text_field( $_POST['level'] ),
            'semester' => sanitize_text_field( $_POST['semester'] ),
        );

        Course::create( $data );

        wp_redirect( admin_url( 'admin.php?page=mtts-courses&message=saved' ) );
        exit;
    }

    public static function calendar() {
        // Handle PDF Upload
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'upload_calendar' && check_admin_referer( 'mtts_upload_calendar' ) ) {
            if ( ! empty( $_FILES['calendar_pdf']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                $uploaded = wp_handle_upload( $_FILES['calendar_pdf'], array( 'test_form' => false ) );
                if ( isset( $uploaded['url'] ) ) {
                    update_option( 'mtts_academic_calendar_url', $uploaded['url'] );
                    echo '<div class="notice notice-success"><p>Calendar uploaded successfully.</p></div>';
                } else {
                     echo '<div class="notice notice-error"><p>Upload failed.</p></div>';
                }
            }
        }

        // Handle Event Addition
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'add_event' && check_admin_referer( 'mtts_add_event' ) ) {
            $session = \MttsLms\Models\Session::get_active_session();
            if ( $session ) {
                \MttsLms\Models\Event::create( array(
                    'title' => sanitize_text_field( $_POST['title'] ),
                    'start_date' => sanitize_text_field( $_POST['start_date'] ),
                    'end_date' => sanitize_text_field( $_POST['end_date'] ),
                    'type' => sanitize_text_field( $_POST['type'] ),
                    'description' => sanitize_textarea_field( $_POST['description'] ),
                    'session_id' => $session->id
                ) );
                echo '<div class="notice notice-success"><p>Event added successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>No active session found.</p></div>';
            }
        }

        $session = \MttsLms\Models\Session::get_active_session();
        $events = $session ? \MttsLms\Models\Event::get_by_session( $session->id ) : array();
        
        include MTTS_LMS_PATH . 'includes/Views/Admin/calendar.php';
    }

    public static function render_settings() {
        include MTTS_LMS_PATH . 'includes/Views/Admin/settings.php';
    }

    public static function render_form_builder() {
        \MttsLms\Controllers\Admin\FormController::render();
    }

    public static function render_campus_centers() {
        $campus_centers = \MttsLms\Models\CampusCenter::all();
        include MTTS_LMS_PATH . 'includes/Views/Admin/campus-centers.php';
    }

    public static function save_campus_center() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        check_admin_referer( 'mtts_save_campus_center' );

        $code = strtoupper( sanitize_text_field( $_POST['code'] ) );

        // Ensure code is unique
        $existing = \MttsLms\Models\CampusCenter::find_by_code( $code );
        if ( $existing ) {
            wp_redirect( add_query_arg( [ 'page' => 'mtts-campus-centers', 'error' => 'duplicate_code' ], admin_url( 'admin.php' ) ) );
            exit;
        }

        \MttsLms\Models\CampusCenter::create( array(
            'name'      => sanitize_text_field( $_POST['name'] ),
            'code'      => $code,
            'city'      => sanitize_text_field( $_POST['city'] ),
            'state'     => sanitize_text_field( $_POST['state'] ),
            'is_active' => 1,
        ) );

        wp_redirect( add_query_arg( [ 'page' => 'mtts-campus-centers', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function render_stakeholders() {
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] === 'register_stakeholder' && check_admin_referer( 'mtts_admin_stakeholder' ) ) {
            self::process_stakeholder_registration();
        }
        include MTTS_LMS_PATH . 'includes/Views/Admin/stakeholder-registration.php';
    }

    private static function process_stakeholder_registration() {
        $name    = sanitize_text_field( $_POST['full_name'] );
        $email   = sanitize_email( $_POST['email'] );
        $role    = sanitize_key( $_POST['role'] );
        
        if ( email_exists( $email ) ) {
            echo '<div class="notice notice-error"><p>User with this email already exists.</p></div>';
            return;
        }

        $user_id = wp_create_user( $email, wp_generate_password(), $email );
        if ( ! is_wp_error( $user_id ) ) {
            wp_update_user( [ 'ID' => $user_id, 'display_name' => $name, 'role' => $role ] );
            
            // If alumni, create student record with 'alumnus' status
            if ( $role === 'mtts_alumni' ) {
                \MttsLms\Models\Student::create( [
                    'user_id'         => $user_id,
                    'matric_number'   => 'ALUM-' . strtoupper( uniqid() ), // Manual alumni gets a placeholder matric
                    'program_id'      => 0,
                    'current_level'   => '0',
                    'admission_year'  => date('Y'),
                    'status'          => 'alumnus'
                ] );
            }

            echo '<div class="notice notice-success"><p>Stakeholder registered successfully!</p></div>';
        }
    }
}
