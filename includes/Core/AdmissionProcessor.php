<?php
namespace MttsLms\Core;

use MttsLms\Models\Application;
use MttsLms\Models\Program;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdmissionProcessor {

    public static function approve_application( $application_id ) {
        $application = Application::find( $application_id );
        if ( ! $application || $application->status !== 'pending' ) {
            return false;
        }

        $program = Program::find( $application->program_id );
        $session = Session::find( $application->session_id );
        
        // 1. Generate Matric Number
        $matric_number = self::generate_matric_number( $program, $session );

        // 2. Create WordPress User
        $user_id = self::create_student_user( $application, $matric_number );

        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        // 3. Create Student Record in Custom Table
        self::create_student_record( $user_id, $application, $matric_number );

        // 4. Update Application Status
        Application::update( $application_id, array( 'status' => 'approved' ) );

        // 5. Send Welcome Email
        $subject = "Welcome to Mountain-Top Theological Seminary - Your Admission Details";
        $message = sprintf(
            "<p>Dear %s,</p>
            <p>Congratulations! Your admission to Mountain-Top Theological Seminary has been approved.</p>
            <p><strong>Your Login Details:</strong><br>
            Username (Matric Number): %s<br>
            Temporary Password: student</p>
            <p>Please login to the <a href='%s'>Student Portal</a> and change your password immediately.</p>",
            esc_html( $application->applicant_name ),
            esc_html( $matric_number ),
            esc_url( home_url( '/portal-login' ) )
        );
        
        \MttsLms\Core\NotificationManager::send_email( $application->email, $subject, $message );

        return true;
    }

    private static function generate_matric_number( $program, $session ) {
        // Format: PROGRAM_CODE/YEAR/SEQUENCE
        // e.g., BTH/2024/001
        
        $year = date('Y');
        if ( $session ) {
            // extract year from session name if possible, else use current year
            if ( preg_match( '/(\d{4})/', $session->name, $matches ) ) {
                $year = $matches[1];
            }
        }
        
        $prefix = $program->code . '/' . $year . '/';
        
        // Count existing students with this prefix to get sequence
        // For now, using a random number or getting last one from DB would be better.
        // Simple implementation: count all students + 1 (prone to race conditions in high traffic, but sufficient for low traffic)
        
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE matric_number LIKE '$prefix%'" );
        $sequence = str_pad( $count + 1, 3, '0', STR_PAD_LEFT );

        return $prefix . $sequence;
    }

    private static function create_student_user( $application, $matric_number ) {
        $password = 'student'; // Default password, should be changed by user
        
        $user_data = array(
            'user_login'    => $matric_number,
            'user_email'    => $application->email,
            'user_pass'     => $password,
            'display_name'  => $application->applicant_name,
            'role'          => 'mtts_student',
        );

        return wp_insert_user( $user_data );
    }

    private static function create_student_record( $user_id, $application, $matric_number ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        
        $form_data = json_decode( $application->form_data, true );

        $data = array(
            'user_id' => $user_id,
            'matric_number' => $matric_number,
            'program_id' => $application->program_id,
            'current_level' => '100', // Default first level
            'admission_year' => date('Y'),
            'date_of_birth' => isset($form_data['dob']) ? $form_data['dob'] : '',
            'gender' => isset($form_data['gender']) ? $form_data['gender'] : '',
            'phone' => $application->phone,
            'address' => isset($form_data['address']) ? $form_data['address'] : '',
            'denomination' => isset($form_data['denomination']) ? $form_data['denomination'] : '',
            'status' => 'active',
        );

        $wpdb->insert( $table, $data );
    }
}
