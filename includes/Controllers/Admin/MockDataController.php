<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MockDataController {

    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'handle_mock_data_generation' ) );
    }

    public static function handle_mock_data_generation() {
        if ( isset( $_GET['mtts_generate_mock_data'] ) && current_user_can( 'manage_options' ) ) {
            check_admin_referer( 'mtts_generate_mock' );
            self::generate_all_mock_users();
            wp_redirect( admin_url( 'admin.php?page=mtts-lms-settings&mock_success=1' ) );
            exit;
        }
    }

    public static function generate_all_mock_users() {
        // 1. Student
        self::create_mock_user( 'mtts_student_test', 'student123', 'student@mtts.edu', 'mtts_student', [
            'matric_number' => 'MTTS/2024/001',
            'current_level' => '100L',
            'department'    => 'Theology',
            'status'        => 'active'
        ]);

        // 2. Lecturer (Academic)
        self::create_mock_user( 'mtts_lecturer_test', 'lecturer123', 'lecturer@mtts.edu', 'mtts_lecturer', [
            'staff_id'      => 'MTTS/STAFF/001',
            'department'    => 'Biblical Studies',
            'designation'   => 'Senior Lecturer'
        ]);

        // 3. Registrar (Non-Academic)
        self::create_mock_user( 'mtts_registrar_test', 'registrar123', 'registrar@mtts.edu', 'mtts_registrar', [
            'staff_id'      => 'MTTS/STAFF/002',
            'department'    => 'Registry'
        ]);

        // 4. Accountant (Non-Academic)
        self::create_mock_user( 'mtts_accountant_test', 'accountant123', 'accountant@mtts.edu', 'mtts_accountant', [
            'staff_id'      => 'MTTS/STAFF/003',
            'department'    => 'Bursary'
        ]);

        // 5. School Admin
        self::create_mock_user( 'mtts_admin_test', 'admin123', 'admin@mtts.edu', 'mtts_school_admin', [
            'staff_id'      => 'MTTS/STAFF/004',
            'department'    => 'ICT'
        ]);

        // 6. Alumni (Stakeholder)
        $alumni_id = self::create_mock_user( 'mtts_alumni_test', 'alumni123', 'alumni@mtts.edu', 'mtts_student', [
            'matric_number' => 'MTTS/2020/055',
            'is_alumni'     => 'yes',
            'graduation_year' => '2022'
        ]);
    }

    private static function create_mock_user( $username, $password, $email, $role, $meta = [] ) {
        if ( ! username_exists( $username ) ) {
            $user_id = wp_create_user( $username, $password, $email );
            if ( ! is_wp_error( $user_id ) ) {
                $user = new \WP_User( $user_id );
                $user->set_role( $role );

                foreach ( $meta as $key => $value ) {
                    update_user_meta( $user_id, $key, $value );
                }

                if ( $role === 'mtts_student' ) {
                    global $wpdb;
                    $wpdb->insert( "{$wpdb->prefix}mtts_students", [
                        'user_id' => $user_id,
                        'matric_number' => $meta['matric_number'] ?? '',
                        'applicant_name' => $username,
                        'current_level' => $meta['current_level'] ?? '100L',
                        'status' => 'active'
                    ]);
                }
                return $user_id;
            }
        }
        return false;
    }
}
