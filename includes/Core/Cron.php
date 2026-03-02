<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cron {

    public static function init() {
        add_filter( 'cron_schedules', array( __CLASS__, 'add_weekly_schedule' ) );
        add_action( 'mtts_weekly_digest_event', array( __CLASS__, 'send_weekly_digests' ) );

        if ( ! wp_next_scheduled( 'mtts_weekly_digest_event' ) ) {
            wp_schedule_event( time(), 'weekly', 'mtts_weekly_digest_event' );
        }
    }

    public static function add_weekly_schedule( $schedules ) {
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly', 'mtts-lms' ),
        );
        return $schedules;
    }

    public static function send_weekly_digests() {
        $students = get_users( array( 'role' => 'mtts_student' ) );

        foreach ( $students as $student_user ) {
            $student_profile = self::get_student_profile( $student_user->ID );
            if ( ! $student_profile ) continue;

            $institution_name = get_option('mtts_institution_name', 'Mountain-Top Theological Seminary');
            $message = "Dear {$student_user->display_name},\n\n";
            $message .= "Here is your weekly progress update from {$institution_name}:\n\n";

            // Unread Messages
            $unread = \MttsLms\Models\Message::count_unread( $student_user->ID );
            $message .= "- You have {$unread} unread message(s) in your inbox.\n";

            // Upcoming Deadlines (simulated for now or fetch from assignments)
            $message .= "- Don't forget to check your dashboard for upcoming assignment deadlines.\n";

            // Forum Activity
            $message .= "- There are new discussions in the student forum. Join the conversation!\n\n";

            $message .= "God bless your studies!\n\nMTTS Administration";

            wp_mail( $student_user->user_email, 'Your Weekly MTTS Progress Digest', $message );
        }
    }

    private static function get_student_profile( $user_id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mtts_students WHERE user_id = %d", $user_id ) );
    }
}
