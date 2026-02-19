<?php
namespace MttsLms\Controllers\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReportsController {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_menus' ) );
        add_action( 'admin_post_mtts_export_students', array( __CLASS__, 'export_students' ) );
    }

    public static function register_menus() {
        add_submenu_page(
            'mtts-lms',
            'Reports',
            'Reports',
            'mtts_view_reports',
            'mtts-reports',
            array( __CLASS__, 'render_reports' )
        );

        add_submenu_page(
            'mtts-lms',
            'Analytics',
            'Analytics',
            'manage_options',
            'mtts-analytics',
            array( __CLASS__, 'render_analytics' )
        );
    }

    public static function render_reports() {
        global $wpdb;
        
        $table_students = $wpdb->prefix . 'mtts_students';
        $table_apps = $wpdb->prefix . 'mtts_applications';
        $table_trans = $wpdb->prefix . 'mtts_transactions';
        $table_progs = $wpdb->prefix . 'mtts_programs';

        // Metrics
        $total_students = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_students}" );
        $total_applications = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_apps}" );
        $pending_applications = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_apps} WHERE status = 'pending'" );
        $total_revenue = $wpdb->get_var( "SELECT SUM(amount) FROM {$table_trans} WHERE status = 'success'" );

        include MTTS_LMS_PATH . 'includes/Views/Admin/reports.php';
    }

    public static function render_analytics() {
        \MttsLms\Controllers\Admin\AnalyticsController::render_dashboard();
    }

    public static function export_students() {
        if ( ! current_user_can( 'mtts_view_reports' ) ) {
            wp_die( 'Unauthorized' );
        }

        check_admin_referer( 'mtts_export_students' );

        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="mtts_students_' . date('Y-m-d') . '.csv"' );

        $output = fopen( 'php://output', 'w' );
        fputcsv( $output, array( 'Matric Number', 'Name', 'Email', 'Program', 'Level', 'Status' ) );

        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        $students = $wpdb->get_results( "SELECT s.*, u.display_name, u.user_email FROM {$table} s LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID ORDER BY s.matric_number ASC" );

        foreach ( $students as $student ) {
            $prog = \MttsLms\Models\Program::find($student->program_id);
            fputcsv( $output, array(
                $student->matric_number,
                $student->display_name,
                $student->user_email,
                $prog ? $prog->code : '',
                $student->current_level,
                $student->status
            ));
        }

        fclose( $output );
        exit;
    }
}
