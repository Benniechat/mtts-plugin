<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Grades {

    /**
     * Map letter grades to points (5.0 scale)
     */
    public static function get_grade_points( $grade ) {
        $grade = strtoupper( trim( $grade ) );
        $map = array(
            'A' => 5,
            'B' => 4,
            'C' => 3,
            'D' => 2,
            'E' => 1,
            'F' => 0,
        );
        return isset( $map[$grade] ) ? $map[$grade] : 0;
    }

    /**
     * Calculate GPA for a specific session
     */
    public static function calculate_gpa( $student_id, $session_id ) {
        global $wpdb;
        $registrations_table = $wpdb->prefix . 'mtts_registrations';
        $courses_table = $wpdb->prefix . 'mtts_courses';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT r.grade, c.credit_unit 
             FROM $registrations_table r
             JOIN $courses_table c ON r.course_id = c.id
             WHERE r.student_id = %d AND r.session_id = %d AND r.grade IS NOT NULL AND r.grade != ''",
            $student_id,
            $session_id
        ) );

        if ( empty( $results ) ) {
            return 0;
        }

        $total_points = 0;
        $total_credits = 0;

        foreach ( $results as $row ) {
            $points = self::get_grade_points( $row->grade );
            $credits = intval( $row->credit_unit );
            $total_points += ( $points * $credits );
            $total_credits += $credits;
        }

        return $total_credits > 0 ? round( $total_points / $total_credits, 2 ) : 0;
    }

    /**
     * Calculate Cumulative GPA (CGPA)
     */
    public static function calculate_cgpa( $student_id ) {
        global $wpdb;
        $registrations_table = $wpdb->prefix . 'mtts_registrations';
        $courses_table = $wpdb->prefix . 'mtts_courses';

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT r.grade, c.credit_unit 
             FROM $registrations_table r
             JOIN $courses_table c ON r.course_id = c.id
             WHERE r.student_id = %d AND r.grade IS NOT NULL AND r.grade != ''",
            $student_id
        ) );

        if ( empty( $results ) ) {
            return 0;
        }

        $total_points = 0;
        $total_credits = 0;

        foreach ( $results as $row ) {
            $points = self::get_grade_points( $row->grade );
            $credits = intval( $row->credit_unit );
            $total_points += ( $points * $credits );
            $total_credits += $credits;
        }

        return $total_credits > 0 ? round( $total_points / $total_credits, 2 ) : 0;
    }

    /**
     * Get Grade Label based on CGPA
     */
    public static function get_class_of_degree( $cgpa ) {
        if ( $cgpa >= 4.50 ) return 'First Class';
        if ( $cgpa >= 3.50 ) return 'Second Class Upper';
        if ( $cgpa >= 2.40 ) return 'Second Class Lower';
        if ( $cgpa >= 1.50 ) return 'Third Class';
        if ( $cgpa >= 1.00 ) return 'Pass';
        return 'Fail';
    }
}
