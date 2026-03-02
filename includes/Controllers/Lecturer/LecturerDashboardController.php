<?php
namespace MttsLms\Controllers\Lecturer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LecturerDashboardController {

    public static function init() {
        add_shortcode( 'mtts_lecturer_dashboard', array( __CLASS__, 'render_dashboard' ) );
        add_action( 'template_redirect', array( __CLASS__, 'check_access' ) );
    }

    public static function check_access() {
        if ( is_page( 'lecturer-dashboard' ) && ! is_user_logged_in() ) {
            auth_redirect();
        }
        
        if ( is_page( 'lecturer-dashboard' ) && ! current_user_can( 'mtts_lecturer' ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    public static function render_dashboard() {
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        ob_start();

        $titles = array(
            'overview'    => array('title' => 'Lecturer Dashboard', 'subtitle' => 'Overview of your academic responsibilities.'),
            'classes'     => array('title' => 'My Courses',         'subtitle' => 'Manage the courses you are currently teaching.'),
            'assignments' => array('title' => 'Assignments',       'subtitle' => 'Create and manage assignments for your students.'),
            'submissions' => array('title' => 'Student Submissions', 'subtitle' => 'Review and grade student assignment submissions.'),
            'students'    => array('title' => 'Student Directory',   'subtitle' => 'Browse and manage students in your courses.'),
            'inbox'       => array('title' => 'Lecturer Inbox',     'subtitle' => 'Communicate with students and administration.'),
            'resources'   => array('title' => 'Academic Resources', 'subtitle' => 'Upload and manage course materials.'),
            'events'      => array('title' => 'Virtual Classroom',  'subtitle' => 'Schedule and manage your live virtual sessions.'),
            'questions'   => array('title' => 'Question Bank',     'subtitle' => 'Manage examination questions for your courses.'),
            'attendance'  => array('title' => 'Attendance Register', 'subtitle' => 'Track and record student attendance.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => ucfirst($view), 'subtitle' => '');
        $page_title    = $current_title['title'];
        $page_subtitle = $current_title['subtitle'];

        // Prepare Sidebar
        $sidebar_path = MTTS_LMS_PATH . 'includes/Views/Lecturer/sidebar.php';

        // Capture Internal View Content
        ob_start();
        switch ( $view ) {
            case 'classes':
                self::render_classes();
                break;
            case 'assignments':
                self::render_assignments();
                break;
            case 'submissions':
                self::render_submissions();
                break;
            case 'students':
                self::render_students();
                break;
            case 'inbox':
                \MttsLms\Controllers\Student\StudentDashboardController::handle_message_send();
                include MTTS_LMS_PATH . 'includes/Views/Lecturer/inbox.php';
                break;
            case 'resources':
                include MTTS_LMS_PATH . 'includes/Views/Lecturer/resources.php';
                break;
            case 'events': // using 'events' for virtual classroom per sidebar link
                include MTTS_LMS_PATH . 'includes/Views/Lecturer/virtual-classes.php';
                break;
            case 'questions':
                self::render_questions();
                break;
            case 'attendance':
                self::render_attendance();
                break;
            default:
                include MTTS_LMS_PATH . 'includes/Views/Lecturer/overview.php';
                break;
        }
        $lms_content = ob_get_clean();

        // Render Scoped Layout
        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Shared/lms-layout.php';
        return ob_get_clean();
    }

    public static function render_classes() {
        $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_courses';
        
        $classes = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table WHERE lecturer_id = %d ORDER BY course_title ASC",
            $user_id
        ) );

        include MTTS_LMS_PATH . 'includes/Views/Lecturer/classes.php';
    }

    private static function render_questions() {
        // Handle Creation
        if ( isset( $_POST['mtts_add_question'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_add_question' ) ) {
            self::add_question();
        }

        $courses = \MttsLms\Models\Course::all(); 
        $selected_course_id = isset( $_GET['course_id'] ) ? intval( $_GET['course_id'] ) : 0;
        
        $questions = [];
        if ( $selected_course_id ) {
            $questions = \MttsLms\Models\Question::get_by_course( $selected_course_id );
        }

        include MTTS_LMS_PATH . 'includes/Views/Lecturer/questions.php';
    }

    private static function add_question() {
        $course_id = intval( $_POST['course_id'] );
        $question_text = sanitize_textarea_field( $_POST['question_text'] );
        $option_a = sanitize_text_field( $_POST['option_a'] );
        $option_b = sanitize_text_field( $_POST['option_b'] );
        $option_c = sanitize_text_field( $_POST['option_c'] );
        $option_d = sanitize_text_field( $_POST['option_d'] );
        $correct_option = sanitize_text_field( $_POST['correct_option'] );
        $points = intval( $_POST['points'] );

        \MttsLms\Models\Question::create( array(
            'course_id' => $course_id,
            'question_text' => $question_text,
            'option_a' => $option_a,
            'option_b' => $option_b,
            'option_c' => $option_c,
            'option_d' => $option_d,
            'correct_option' => $correct_option,
            'points' => $points,
        ) );

        wp_redirect( add_query_arg( array( 'view' => 'questions', 'course_id' => $course_id, 'status' => 'added' ), get_permalink() ) );
        exit;
    }

    private static function render_attendance() {
        // Handle saving
        if ( isset( $_POST['mtts_save_attendance'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_save_attendance' ) ) {
            self::save_attendance();
        }

        $courses = \MttsLms\Models\Course::all(); // Should filter by lecturer in real app
        $selected_course_id = isset( $_GET['course_id'] ) ? intval( $_GET['course_id'] ) : 0;
        $selected_date = isset( $_GET['date'] ) ? sanitize_text_field( $_GET['date'] ) : date('Y-m-d');
        
        $students = [];
        $existing_attendance = [];

        if ( $selected_course_id ) {
            // Get students registered for this course
            // Complex query needed ideally, doing simple 2-step for now
            // 1. Get registrations for course
            global $wpdb;
            $reg_table = $wpdb->prefix . 'mtts_registrations';
            $stu_table = $wpdb->prefix . 'mtts_students';
            
            $students = $wpdb->get_results( $wpdb->prepare( 
                "SELECT s.* FROM {$stu_table} s 
                JOIN {$reg_table} r ON s.id = r.student_id 
                WHERE r.course_id = %d AND r.status = 'registered'", 
                $selected_course_id 
            ) );

            // 2. Get existing attendance
            $existing_attendance = \MttsLms\Models\Attendance::get_by_course_and_date( $selected_course_id, $selected_date );
        }

        include MTTS_LMS_PATH . 'includes/Views/Lecturer/attendance.php';
    }

    private static function save_attendance() {
        $course_id = intval( $_POST['course_id'] );
        $date = sanitize_text_field( $_POST['date'] );
        $attendance_data = $_POST['attendance']; // array of student_id => status
        $session = \MttsLms\Models\Session::get_active_session();

        if ( ! $session ) return;

        foreach ( $attendance_data as $student_id => $status ) {
            $student_id = intval( $student_id );
            $status = sanitize_text_field( $status );

            // Check if exists
            global $wpdb;
            $table = \MttsLms\Models\Attendance::get_table_name();
            $exists = $wpdb->get_var( $wpdb->prepare( 
                "SELECT id FROM {$table} WHERE course_id = %d AND student_id = %d AND class_date = %s",
                $course_id, $student_id, $date
            ) );

            if ( $exists ) {
                \MttsLms\Models\Attendance::update( $exists, array( 'status' => $status ) );
            } else {
                \MttsLms\Models\Attendance::create( array(
                    'course_id' => $course_id,
                    'student_id' => $student_id,
                    'session_id' => $session->id,
                    'class_date' => $date,
                    'status' => $status
                ) );
            }
        }
        
        // Redirect to avoid resubmission
        $redirect_url = add_query_arg( array(
            'view' => 'attendance',
            'course_id' => $course_id,
            'date' => $date,
            'status' => 'saved'
        ), get_permalink() );
        
        wp_redirect( $redirect_url );
        exit;
    }

    private static function render_assignments() {
        // Handle Creation
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'create_assignment' && check_admin_referer( 'mtts_create_assignment' ) ) {
            $session = \MttsLms\Models\Session::get_active_session();
            if ( $session ) {
                \MttsLms\Models\Assignment::create( array(
                    'course_id' => intval( $_POST['course_id'] ),
                    'session_id' => $session->id,
                    'title' => sanitize_text_field( $_POST['title'] ),
                    'description' => sanitize_textarea_field( $_POST['description'] ),
                    'due_date' => sanitize_text_field( $_POST['due_date'] ),
                    'total_points' => intval( $_POST['total_points'] ),
                ) );
                echo '<div class="mtts-alert mtts-alert-success">Assignment created successfully.</div>';
            }
        }

        $courses = \MttsLms\Models\Course::all(); 
        $session = \MttsLms\Models\Session::get_active_session();
        $assignments = [];
        
        if ( $session ) {
            foreach ($courses as $c) {
                // Simplified fetching
                $course_assignments = \MttsLms\Models\Assignment::get_by_course( $c->id, $session->id );
                if ( $course_assignments ) {
                    $assignments = array_merge( $assignments, $course_assignments );
                }
            }
        }
        
        include MTTS_LMS_PATH . 'includes/Views/Lecturer/assignments.php';
    }

    private static function render_submissions() {
        $assignment_id = intval( $_GET['assignment_id'] );
        
        // Handle Grading
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'grade_submission' && check_admin_referer( 'mtts_grade_submission' ) ) {
             $sub_id = intval( $_POST['submission_id'] );
             $grade = floatval( $_POST['grade'] );
             \MttsLms\Models\Submission::update( $sub_id, array( 'grade' => $grade ) );
             echo '<div class="mtts-alert mtts-alert-success">Grade saved.</div>';
        }

        $submissions = \MttsLms\Models\Submission::get_by_assignment( $assignment_id );
        include MTTS_LMS_PATH . 'includes/Views/Lecturer/submissions.php';
    }

    private static function render_students() {
        // Handle Bonus
        if ( isset( $_POST['mtts_action'] ) && $_POST['mtts_action'] == 'award_bonus' && check_admin_referer( 'mtts_award_bonus' ) ) {
            $student_id = intval( $_POST['student_id'] );
            $course_id = intval( $_POST['course_id'] );
            $marks = floatval( $_POST['marks'] );
            $reason = sanitize_text_field( $_POST['reason'] );
            
            \MttsLms\Models\BonusMark::create( array(
                'student_id' => $student_id,
                'course_id' => $course_id,
                'lecturer_id' => get_current_user_id(),
                'marks' => $marks,
                'reason' => $reason
            ) );
            
            echo '<div class="mtts-alert mtts-alert-success">Bonus awarded successfully.</div>';
        }

        $courses = \MttsLms\Models\Course::all(); 
        $selected_course_id = isset( $_GET['course_id'] ) ? intval( $_GET['course_id'] ) : 0;
        $students = [];
        
        if ( $selected_course_id ) {
            global $wpdb;
            $reg_table = $wpdb->prefix . 'mtts_registrations';
            $stu_table = $wpdb->prefix . 'mtts_students';
            $students = $wpdb->get_results( $wpdb->prepare( 
                "SELECT s.* FROM {$stu_table} s 
                JOIN {$reg_table} r ON s.id = r.student_id 
                WHERE r.course_id = %d AND r.status = 'registered'", 
                $selected_course_id 
            ) );
        }

        include MTTS_LMS_PATH . 'includes/Views/Lecturer/students.php';
    }
}
