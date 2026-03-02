<?php
namespace MttsLms\Controllers\Admin;

use MttsLms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FormEntryController {
    
    public static function init() {
        add_action( 'admin_post_mtts_update_entry_status', array( __CLASS__, 'handle_status_update' ) );
        add_action( 'admin_post_mtts_export_entries_csv', array( __CLASS__, 'handle_csv_export' ) );
    }

    public static function render() {
        $action  = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : 'list';
        $entry_id = isset( $_GET['entry_id'] ) ? intval( $_GET['entry_id'] ) : 0;
        $form_id  = isset( $_GET['form_id'] ) ? intval( $_GET['form_id'] ) : 0;

        if ( 'view' === $action ) {
            self::render_details( $entry_id );
        } else {
            self::render_list( $form_id );
        }
    }

    private static function render_list( $form_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_form_entries';
        
        $query = "SELECT e.*, f.title as form_title FROM {$table} e JOIN {$wpdb->prefix}mtts_forms f ON e.form_id = f.id";
        if ( $form_id ) {
            $query .= $wpdb->prepare( " WHERE e.form_id = %d", $form_id );
        }
        $query .= " ORDER BY e.created_at DESC";
        
        $entries = $wpdb->get_results( $query );
        $forms   = Form::all();

        include MTTS_LMS_PATH . 'includes/Views/Admin/form-entries-list.php';
    }

    private static function render_details( $entry_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_form_entries';
        $entry = $wpdb->get_row( $wpdb->prepare( 
            "SELECT e.*, f.title as form_title, f.form_data FROM {$table} e JOIN {$wpdb->prefix}mtts_forms f ON e.form_id = f.id WHERE e.id = %d", 
            $entry_id 
        ) );

        if ( ! $entry ) {
            wp_die( 'Entry not found.' );
        }

        include MTTS_LMS_PATH . 'includes/Views/Admin/form-entry-details.php';
    }

    public static function handle_status_update() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'mtts_update_entry_status' );

        $entry_id = intval( $_POST['entry_id'] );
        $status   = sanitize_key( $_POST['status'] );
        $remarks  = sanitize_textarea_field( $_POST['remarks'] ?? '' );
        
        global $wpdb;
        $entry = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM {$wpdb->prefix}mtts_form_entries WHERE id = %d", 
            $entry_id 
        ) );

        if ( ! $entry ) wp_die( 'Entry not found' );

        $user_id    = $entry->user_id;
        $entry_data = json_decode( $entry->entry_data, true );

        // Handle Status Transitions
        if ( 'approved' === $status && 'approved' !== $entry->status ) {
            self::process_approval( $entry, $user_id, $entry_data );
        } elseif ( 'rejected' === $status ) {
            self::process_rejection( $entry, $user_id, $remarks );
        }

        // Update Entry Status
        $wpdb->update( 
            $wpdb->prefix . 'mtts_form_entries', 
            array( 'status' => $status ), 
            array( 'id' => $entry_id ) 
        );

        // Audit Log
        self::log_audit_action( $entry_id, $status, $remarks );

        wp_redirect( admin_url( 'admin.php?page=mtts-form-entries&action=view&entry_id=' . $entry_id . '&updated=1' ) );
        exit;
    }

    private static function process_approval( $entry, $user_id, $entry_data ) {
        global $wpdb;
        
        // 1. Generate Matric Number
        $campus = $entry_data['Preferred Campus'] ?? 'Lagos';
        require_once MTTS_LMS_PATH . 'includes/Helpers/MatricNumberHelper.php';
        $matric = \MttsLms\Helpers\MatricNumberHelper::generate( $campus );

        // 2. Update User Account
        // WP doesn't allow changing user_login via wp_update_user easily
        $wpdb->update( $wpdb->users, array( 'user_login' => $matric ), array( 'ID' => $user_id ) );
        
        wp_update_user( array(
            'ID'            => $user_id,
            'user_pass'     => 'student',
            'display_name'  => $entry_data['Surname'] . ' ' . ($entry_data['Forenames'] ?? ''),
        ) );

        update_user_meta( $user_id, 'mtts_account_status', 'active' );
        update_user_meta( $user_id, 'mtts_matric_number', $matric );
        update_user_meta( $user_id, 'mtts_force_password_change', 1 );

        // 3. Create Student Record
        $program_name = $entry_data['Program of Choice'] ?? '';
        $program_id = 0;
        $program = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}mtts_programs WHERE name LIKE %s", '%' . $program_name . '%' ) );
        if ( $program ) $program_id = $program->id;

        $campus_id = 0;
        $campus_row = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}mtts_campus_centers WHERE name LIKE %s", '%' . $campus . '%' ) );
        if ( $campus_row ) $campus_id = $campus_row->id;

        $wpdb->insert( $wpdb->prefix . 'mtts_students', array(
            'user_id'          => $user_id,
            'matric_number'    => $matric,
            'program_id'       => $program_id,
            'campus_center_id' => $campus_id,
            'current_level'    => '100',
            'admission_year'   => date('Y'),
            'phone'            => $entry_data['Phone/GSM Number'] ?? '',
            'status'           => 'active'
        ) );

        // 4. Send Congratulations Message
        self::send_ai_congratulations( $user_id, $matric, $entry_data, $campus );
    }

    private static function process_rejection( $entry, $user_id, $remarks ) {
        update_user_meta( $user_id, 'mtts_account_status', 'rejected' );
        
        $user = get_userdata( $user_id );
        if ( ! $user ) return;

        $subject = "Application Status Update - MTTS";
        $message = "Dear Applicant,\n\nWe regret to inform you that your application has been rejected.\n\n";
        if ( $remarks ) {
            $message .= "Remarks: " . $remarks . "\n\n";
        }
        $message .= "Best regards,\nMTTS Admissions Team";
        
        wp_mail( $user->user_email, $subject, $message );
    }

    private static function send_ai_congratulations( $user_id, $matric, $entry_data, $campus ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) return;

        $program = $entry_data['Program of Choice'] ?? 'Chosen Program';
        $full_name = $entry_data['Surname'] . ' ' . ($entry_data['Forenames'] ?? '');
        
        // AI Rewriting Logic
        $template = get_option( 'mtts_ai_congratulations_template' );
        if ( ! $template ) {
            $template = "Write a formal, encouraging, and spiritually aligned congratulations message for a new student joining Mountain Top Theological Seminary. \nStudent Name: {name}\nProgram: {program}\nCampus: {campus}\nMatric Number: {matric}\nInclude login instructions: Username is the Matric Number and temporary password is 'student'. Mention that they must change their password on first login.";
        }

        $prompt = str_replace(
            array( '{name}', '{program}', '{campus}', '{matric}' ),
            array( $full_name, $program, $campus, $matric ),
            $template
        );
        
        require_once MTTS_LMS_PATH . 'includes/Core/AI/AINotificationGenerator.php';
        $ai_message = \MttsLms\Core\AI\AINotificationGenerator::generate($prompt);
        
        $subject = "Congratulations on Your Admission - Mountain Top Theological Seminary";
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail( $user->user_email, $subject, wpautop($ai_message), $headers );
    }

    private static function log_audit_action( $entry_id, $status, $remarks ) {
        $current_user = wp_get_current_user();
        
        $log_data = array(
            'actor_name'  => $current_user->display_name,
            'action'      => $status,
            'remarks'     => $remarks,
            'action_time' => current_time('mysql')
        );
        
        $audit_key = "mtts_audit_entry_" . $entry_id;
        $audit_logs = get_option($audit_key, array());
        $audit_logs[] = $log_data;
        update_option($audit_key, $audit_logs);
    }


    public static function handle_csv_export() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        
        $form_id = intval( $_GET['form_id'] );
        $form    = Form::find( $form_id );
        
        if ( ! $form ) wp_die( 'Form not found.' );

        global $wpdb;
        $entries = $wpdb->get_results( $wpdb->prepare( 
            "SELECT entry_data, status, created_at FROM {$wpdb->prefix}mtts_form_entries WHERE form_id = %d", 
            $form_id 
        ) );

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="' . $form->form_slug . '-entries.csv"' );

        $output = fopen( 'php://output', 'w' );
        
        if ( ! empty( $entries ) ) {
            $first_entry = json_decode( $entries[0]->entry_data, true );
            $headers = array_keys( $first_entry );
            $headers[] = 'Status';
            $headers[] = 'Submitted At';
            fputcsv( $output, $headers );

            foreach ( $entries as $entry ) {
                $data = json_decode( $entry->entry_data, true );
                $row = array_values( $data );
                $row[] = ucfirst( $entry->status );
                $row[] = $entry->created_at;
                fputcsv( $output, $row );
            }
        }
        
        fclose( $output );
        exit;
    }
}
