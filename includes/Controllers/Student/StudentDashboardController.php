<?php
namespace MttsLms\Controllers\Student;

use MttsLms\Models\Student;
use MttsLms\Models\Program;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class StudentDashboardController {

    public static function init() {
        add_shortcode( 'mtts_student_dashboard', array( __CLASS__, 'render_dashboard' ) );
        add_action( 'template_redirect', array( __CLASS__, 'check_access' ) );
    }

    public static function check_access() {
        if ( is_page( 'student-dashboard' ) && ! is_user_logged_in() ) {
            auth_redirect();
        }
        
        if ( is_page( 'student-dashboard' ) && ! current_user_can( 'mtts_student' ) ) {
            wp_redirect( home_url() );
            exit;
        }

        // Force Password Change
        if ( is_page( 'student-dashboard' ) && is_user_logged_in() ) {
            $user_id = get_current_user_id();
            if ( get_user_meta( $user_id, 'mtts_force_password_change', true ) ) {
                $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : '';
                if ( 'change-password' !== $view ) {
                    wp_redirect( add_query_arg( 'view', 'change-password', get_permalink() ) );
                    exit;
                }
            }
        }
    }

    public static function render_dashboard() {
        $user_id = get_current_user_id();
        $student = self::get_student_profile( $user_id );

        if ( ! $student ) {
            return '<div class="mtts-alert mtts-alert-danger">Student profile not found. Please contact support.</div>';
        }

        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        ob_start();

        $titles = array(
            'overview'    => array('title' => 'Student Dashboard', 'subtitle' => 'Welcome back to your academic portal.'),
            'profile'     => array('title' => 'My Profile',         'subtitle' => 'Manage your personal and academic information.'),
            'courses'     => array('title' => 'My Courses',         'subtitle' => 'View and register for your academic courses.'),
            'payments'    => array('title' => 'Payments & Fees',    'subtitle' => 'Track your financial records and make payments.'),
            'exams'       => array('title' => 'Exam Center',        'subtitle' => 'Take your scheduled examinations here.'),
            'results'     => array('title' => 'Exam Results',       'subtitle' => 'View your academic performance and grades.'),
            'wallet'      => array('title' => 'My Wallet',         'subtitle' => 'Manage your digital credits and transactions.'),
            'calendar'    => array('title' => 'Academic Calendar',   'subtitle' => 'Stay updated with important school dates.'),
            'assignments' => array('title' => 'Assignments',       'subtitle' => 'Submit and track your coursework.'),
            'inbox'       => array('title' => 'Message Center',     'subtitle' => 'Communicate with lecturers and administration.'),
            'forum'       => array('title' => 'Community Forum',    'subtitle' => 'Engage in discussions with your fellow students.'),
            'resources'   => array('title' => 'Academic Resources', 'subtitle' => 'Access study materials and library resources.'),
            'badges'      => array('title' => 'My Achievements',   'subtitle' => 'View your earned badges and rewards.'),
            'portfolio'   => array('title' => 'Academic Portfolio', 'subtitle' => 'Showcase your best academic work.'),
            'change-password' => array('title' => 'Security Update', 'subtitle' => 'Please update your password to continue.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => ucfirst($view), 'subtitle' => '');
        $page_title    = $current_title['title'];
        $page_subtitle = $current_title['subtitle'];

        // Prepare Sidebar
        $sidebar_path = MTTS_LMS_PATH . 'includes/Views/Student/sidebar.php';

        // Capture Internal View Content
        ob_start();
        switch ( $view ) {
            case 'profile':
                include MTTS_LMS_PATH . 'includes/Views/Student/profile.php';
                break;
            case 'courses':
                self::render_courses( $student );
                break;
            case 'payments':
                \MttsLms\Controllers\Student\PaymentController::process( $student );
                break;
            case 'id-card':
                include MTTS_LMS_PATH . 'includes/Views/Student/id-card.php';
                break;
            case 'exams':
                \MttsLms\Controllers\Student\ExamController::process( $student );
                break;
            case 'results':
                include MTTS_LMS_PATH . 'includes/Views/Student/exam-results.php';
                break;
            case 'wallet':
                \MttsLms\Controllers\Student\WalletController::process( $student );
                break;
            case 'calendar':
                $session = \MttsLms\Models\Session::get_active_session();
                $events = $session ? \MttsLms\Models\Event::get_by_session( $session->id ) : array();
                include MTTS_LMS_PATH . 'includes/Views/Student/calendar.php';
                break;
            case 'assignments':
                self::render_assignments( $student );
                break;
            case 'inbox':
                self::handle_message_send();
                include MTTS_LMS_PATH . 'includes/Views/Student/inbox.php';
                break;
            case 'forum':
                include MTTS_LMS_PATH . 'includes/Views/Student/forum.php';
                break;
            case 'resources':
                include MTTS_LMS_PATH . 'includes/Views/Student/resources.php';
                break;
            case 'badges':
                include MTTS_LMS_PATH . 'includes/Views/Student/badges.php';
                break;
            case 'portfolio':
                echo \MttsLms\Controllers\PortfolioController::render_portfolio_view( $student );
                break;
            case 'change-password':
                self::render_change_password();
                break;
            default:
                include MTTS_LMS_PATH . 'includes/Views/Student/overview.php';
                break;
        }
        $lms_content = ob_get_clean();

        // Render Scoped Layout
        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Shared/lms-layout.php';
        return ob_get_clean();
    }

    private static function get_student_profile( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_students';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_id = %d", $user_id ) );
    }

    private static function render_courses( $student ) {
        // Handle Form Submission
        if ( isset( $_POST['mtts_register_courses'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_register_courses' ) ) {
            self::process_course_registration( $student );
        }

        $session = Session::get_active_session();
        
        // simple fallback if no active session
        if ( ! $session ) {
            echo '<div class="mtts-alert mtts-alert-warning">No active academic session.</div>';
            return;
        }

        // Fetch available courses for Student's Program and Level
        // Note: Students usually register for both semesters or current semester. 
        // For simplicity, showing all courses for the level.
        $available_courses = \MttsLms\Models\Course::get_by_program_and_level( $student->program_id, $student->current_level );
        
        // Fetch already registered courses
        $registered_courses = \MttsLms\Models\Registration::get_student_courses( $student->id, $session->id );

        include MTTS_LMS_PATH . 'includes/Views/Student/courses.php';
    }

    private static function process_course_registration( $student ) {
        $session = Session::get_active_session();
        if ( ! $session ) return;

        if ( ! empty( $_POST['course_ids'] ) ) {
            foreach ( $_POST['course_ids'] as $course_id ) {
                $course_id = intval( $course_id );
                $course = \MttsLms\Models\Course::find( $course_id );
                
                if ( $course ) {
                    $data = array(
                        'student_id' => $student->id,
                        'course_id' => $course_id,
                        'session_id' => $session->id,
                        'semester' => $course->semester,
                        'status' => 'registered',
                    );
                    
                    // Check if already registered handled by unique key DB constraint or check here
                    \MttsLms\Models\Registration::create( $data );
                }
            }
        }
    }

    private static function render_assignments( $student ) {
        // Handle Submission
        $message = '';
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'submit_assignment' && check_admin_referer( 'mtts_submit_assignment' ) ) {
            $assignment_id = intval( $_POST['assignment_id'] );
            $content = sanitize_textarea_field( $_POST['content'] );
            
            // Save Submission
            $submission_id = \MttsLms\Models\Submission::create( array(
                'student_id' => $student->id,
                'assignment_id' => $assignment_id,
                'content' => $content,
                'status' => 'submitted'
            ) );

            if ( $submission_id ) {
                $message = '<div class="mtts-alert mtts-alert-success">Assignment submitted successfully!</div>';
                
                // Trigger Plagiarism Check
                \MttsLms\Core\PlagiarismChecker::check( $submission_id );
            } else {
                 $message = '<div class="mtts-alert mtts-alert-danger">Submission failed. Try again.</div>';
            }
        }

        $session = Session::get_active_session();
        $assignments = [];
        
        // Fetch registered courses
        $registered_courses = \MttsLms\Models\Registration::get_student_courses( $student->id, $session->id );
        
        if ( $registered_courses ) {
            foreach ( $registered_courses as $reg ) {
                $course_assignments = \MttsLms\Models\Assignment::get_by_course( $reg->course_id, $session->id );
                if ( $course_assignments ) {
                    $assignments = array_merge( $assignments, $course_assignments );
                }
            }
        }

        include MTTS_LMS_PATH . 'includes/Views/Student/assignments.php';
    }

    public static function handle_message_send() {
        if (
            isset( $_POST['mtts_action'] ) &&
            $_POST['mtts_action'] === 'send_message' &&
            \MttsLms\Core\Security::check_request( 'mtts_send_message' )
        ) {
            $receiver_id = intval( $_POST['receiver_id'] );
            $post_data   = \MttsLms\Core\Security::sanitize_deep( $_POST );
            $subject     = $post_data['subject'] ?? '';
            $body        = $post_data['body'] ?? '';
            $parent_id   = ! empty( $post_data['parent_id'] ) ? intval( $post_data['parent_id'] ) : null;

            if ( $receiver_id && $body ) {
                \MttsLms\Models\Message::send(
                    get_current_user_id(),
                    $receiver_id,
                    $subject,
                    $body,
                    $parent_id
                );
                // Redirect to inbox to prevent re-submission
                wp_redirect( remove_query_arg( [ 'compose' ], add_query_arg( 'view', 'inbox', get_permalink() ) ) );
                exit;
            }
        }
    }
    public static function render_change_password() {
        if ( isset( $_POST['mtts_change_password'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_change_password' ) ) {
            $pass1 = $_POST['pass1'];
            $pass2 = $_POST['pass2'];
            
            if ( $pass1 === $pass2 && strlen($pass1) >= 8 ) {
                wp_set_password( $pass1, get_current_user_id() );
                delete_user_meta( get_current_user_id(), 'mtts_force_password_change' );
                echo '<div class="mtts-alert mtts-alert-success">Password updated successfully! Redirecting...</div>';
                echo '<script>setTimeout(() => { window.location.href = "' . get_permalink() . '"; }, 2000);</script>';
                return;
            } else {
                echo '<div class="mtts-alert mtts-alert-danger">Passwords do not match or are too short (min 8 chars).</div>';
            }
        }

        include MTTS_LMS_PATH . 'includes/Views/Student/change-password.php';
    }
}
