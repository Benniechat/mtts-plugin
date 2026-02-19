<?php
namespace MttsLms\Controllers\Student;

use MttsLms\Models\Question;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ExamController {

    public static function process( $student ) {
        if ( isset( $_GET['action'] ) && $_GET['action'] == 'take_exam' && isset( $_GET['course_id'] ) ) {
            self::take_exam( $student, intval( $_GET['course_id'] ) );
        } elseif ( isset( $_POST['mtts_submit_exam'] ) ) {
            self::submit_exam( $student );
        } else {
            // List available exams (courses with questions)
             // For simplify, just showing a list in the main dashboard or a separate tab
             // But for now, let's assume the link comes from the Course list
             echo 'Select a course to take exam.'; 
        }
    }

    private static function take_exam( $student, $course_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_exam_results';
        
        // Check for existing attempt
        $attempt = $wpdb->get_row( $wpdb->prepare( 
            "SELECT * FROM {$table} WHERE student_id = %d AND course_id = %d", 
            $student->id, $course_id 
        ) );

        $course = \MttsLms\Models\Course::find( $course_id );
        $duration_seconds = ($course->exam_duration ? $course->exam_duration : 60) * 60; // Default 60 mins

        if ( $attempt ) {
            if ( $attempt->status == 'submitted' ) {
                echo '<div class="mtts-alert mtts-alert-warning">You have already completed this exam.</div>';
                return;
            }

            // Check if time expired
            $started_at = strtotime( $attempt->started_at );
            $elapsed = time() - $started_at;
            
            if ( $elapsed >= $duration_seconds ) {
                // Time expired, force submit (or just block)
                // Ideally, auto-grade what they have or 0
                self::force_submit( $student, $course_id, $attempt->id );
                return;
            }
            
            $remaining_time = $duration_seconds - $elapsed;
        } else {
            // Start new attempt
            $wpdb->insert(
                $table,
                array(
                    'student_id' => $student->id,
                    'course_id' => $course_id,
                    'session_id' => \MttsLms\Models\Session::get_active_session()->id,
                    'status' => 'started',
                    'started_at' => current_time( 'mysql' ),
                    'score' => 0,
                    'total_questions' => 0
                )
            );
            $remaining_time = $duration_seconds;
        }

        $questions = Question::get_by_course( $course_id );
        
        if ( empty( $questions ) ) {
            echo '<div class="mtts-alert mtts-alert-info">No questions available for this exam yet.</div>';
            return;
        }

        // Shuffle questions for randomization
        shuffle( $questions );

        include MTTS_LMS_PATH . 'includes/Views/Student/exam-take.php';
    }

    private static function force_submit( $student, $course_id, $attempt_id ) {
         global $wpdb;
         $table = $wpdb->prefix . 'mtts_exam_results';
         
         // Update status to submitted
         $wpdb->update( 
             $table, 
             array( 'status' => 'submitted', 'submitted_at' => current_time( 'mysql' ) ), 
             array( 'id' => $attempt_id ) 
         );
         
         echo '<div class="mtts-alert mtts-alert-danger">Time expired. Exam submitted automatically.</div>';
         echo '<p><a href="?view=results&course_id=' . $course_id . '" class="mtts-btn">View Results</a></p>';
    }

    private static function submit_exam( $student ) {
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'mtts_submit_exam' ) ) {
            return;
        }

        $course_id = intval( $_POST['course_id'] );
        $answers   = \MttsLms\Core\Security::sanitize_deep( $_POST['answers'] ?? [] );
        $session   = Session::get_active_session();

        // Calculate Score
        $questions = Question::get_by_course( $course_id );
        $total_questions = count( $questions );
        $score = 0;
        $total_points = 0;

        foreach ( $questions as $q ) {
            $total_points += $q->points;
            if ( isset( $answers[$q->id] ) && $answers[$q->id] == $q->correct_option ) {
                $score += $q->points;
            }
        }

        // Add Bonus Marks
        $bonus = \MttsLms\Models\BonusMark::get_student_bonus( $student->id, $course_id );
        $score += $bonus;

        // Update Result (since row created at start)
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_exam_results';
        
        $wpdb->update(
            $table,
            array(
                'score' => $score,
                'total_questions' => $total_questions,
                'answers' => json_encode( $answers ),
                'status' => 'submitted',
                'submitted_at' => current_time( 'mysql' )
            ),
            array(
                'student_id' => $student->id, 
                'course_id' => $course_id
            )
        );

        // Calculate percentage
        $score_percentage = $total_points > 0 ? ( $score / $total_points ) * 100 : 0;

        if ( $score_percentage >= 80 ) {
            \MttsLms\Models\Badge::check_and_award( get_current_user_id(), 'exam_score_80', $score_percentage );
        }

        // Redirect to results or dashboard
        wp_redirect( add_query_arg( array( 'view' => 'results', 'course_id' => $course_id, 'score' => $score ), remove_query_arg( 'action' ) ) );
        exit;
    }
}
