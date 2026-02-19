<?php
namespace MttsLms\Core\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Migration {

    public static function run() {
        self::create_tables();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // Table: mtts_programs
        $table_name = $wpdb->prefix . 'mtts_programs';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            code varchar(50) NOT NULL,
            duration_years int(2) NOT NULL DEFAULT 4,
            levels int(2) NOT NULL DEFAULT 4,
            certificate_type varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql );

        // Table: mtts_sessions
        $table_name = $wpdb->prefix . 'mtts_sessions';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL, -- e.g., 2024/2025
            start_date date NOT NULL,
            end_date date NOT NULL,
            status varchar(20) DEFAULT 'inactive', -- active, inactive
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql );

        // Table: mtts_campus_centers
        $table_name = $wpdb->prefix . 'mtts_campus_centers';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            code varchar(10) NOT NULL,
            city varchar(100) DEFAULT '',
            state varchar(100) DEFAULT '',
            is_active tinyint(1) DEFAULT 1,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code)
        ) $charset_collate;";
        dbDelta( $sql );

        // Seed default campus centers if table is empty
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mtts_campus_centers" );
        if ( $count == 0 ) {
            $defaults = [
                [ 'Lagos', 'LAG', 'Lagos', 'Lagos' ],
                [ 'Abuja', 'ABJ', 'Abuja', 'FCT' ],
                [ 'Port Harcourt', 'PHC', 'Port Harcourt', 'Rivers' ],
                [ 'Ibadan', 'IBD', 'Ibadan', 'Oyo' ],
                [ 'Kano', 'KAN', 'Kano', 'Kano' ],
                [ 'Enugu', 'ENU', 'Enugu', 'Enugu' ],
                [ 'Online/Overseas', 'ONL', '', '' ],
            ];
            foreach ( $defaults as $d ) {
                $wpdb->insert( $wpdb->prefix . 'mtts_campus_centers', [
                    'name' => $d[0], 'code' => $d[1], 'city' => $d[2], 'state' => $d[3]
                ] );
            }
        }

        // Table: mtts_students (extends WP users technically, but stores extra meta or direct relation)
        // Note: Main auth is WP users. This table stores academic specific profile.
        $table_name = $wpdb->prefix . 'mtts_students';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            matric_number varchar(50) NOT NULL UNIQUE,
            program_id mediumint(9) NOT NULL,
            campus_center_id mediumint(9) DEFAULT NULL,
            current_level varchar(50) NOT NULL,
            admission_year int(4) NOT NULL,
            date_of_birth date DEFAULT '0000-00-00',
            gender varchar(20) DEFAULT '',
            phone varchar(20) DEFAULT '',
            address text DEFAULT '',
            denomination varchar(100) DEFAULT '',
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta( $sql );

         // Table: mtts_courses
         $table_name = $wpdb->prefix . 'mtts_courses';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             course_code varchar(20) NOT NULL UNIQUE,
             course_title varchar(255) NOT NULL,
             credit_unit int(2) NOT NULL DEFAULT 0,
             program_id mediumint(9) NOT NULL,
             level varchar(50) NOT NULL,
             semester varchar(20) NOT NULL DEFAULT '1', -- 1 or 2
             department_id mediumint(9) DEFAULT 0,
             exam_duration int(3) DEFAULT 60, -- in minutes
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_applications
         $table_name = $wpdb->prefix . 'mtts_applications';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             applicant_name varchar(255) NOT NULL,
             email varchar(100) NOT NULL,
             phone varchar(20) DEFAULT '',
             program_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             form_data longtext NOT NULL, -- JSON data of the application
             status varchar(20) DEFAULT 'pending', -- pending, approved, rejected
             payment_status varchar(20) DEFAULT 'unpaid',
             submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // ... (Skipping mtts_registrations, mtts_transactions, mtts_attendance, mtts_assignments, mtts_submissions, mtts_questions for brevity in tool call, but ensuring DBDelta handles them if I don't touch them. Wait, ReplaceFileContent replaces a block. I must be careful.)
         // Actually, I should just target the specific blocks.
         
         // Let's do multiple replacements.

         // Table: mtts_registrations (Student Course Registration)
         $table_name = $wpdb->prefix . 'mtts_registrations';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             student_id mediumint(9) NOT NULL, -- link to mtts_students.id
             course_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             semester varchar(20) NOT NULL,
             score_ca float DEFAULT 0,
             score_exam float DEFAULT 0,
             total_score float DEFAULT 0,
             grade varchar(5) DEFAULT '',
             status varchar(20) DEFAULT 'registered', -- registered, approved
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             UNIQUE KEY student_course (student_id, course_id, session_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_transactions (Fee Payments)
         $table_name = $wpdb->prefix . 'mtts_transactions';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             student_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             reference varchar(100) NOT NULL UNIQUE,
             amount decimal(10,2) NOT NULL,
             gateway varchar(50) NOT NULL, -- paystack, flutterwave, manual
             status varchar(20) DEFAULT 'pending', -- pending, success, failed
             purpose varchar(100) DEFAULT 'tuition', -- tuition, acceptance_fee, etc
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             paid_at datetime DEFAULT NULL,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_attendance
         $table_name = $wpdb->prefix . 'mtts_attendance';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             course_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             student_id mediumint(9) NOT NULL,
             class_date date NOT NULL,
             status varchar(20) NOT NULL DEFAULT 'present', -- present, absent, excused
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             UNIQUE KEY student_class_date (student_id, course_id, class_date)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_assignments
         $table_name = $wpdb->prefix . 'mtts_assignments';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             course_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             title varchar(255) NOT NULL,
             description longtext DEFAULT '',
             due_date datetime NOT NULL,
             total_points int(3) DEFAULT 100,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_submissions
         $table_name = $wpdb->prefix . 'mtts_submissions';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             assignment_id mediumint(9) NOT NULL,
             student_id mediumint(9) NOT NULL,
             content longtext DEFAULT '', -- Text or File URL
             submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
             grade float DEFAULT NULL,
             feedback longtext DEFAULT '',
             plagiarism_score float DEFAULT 0,
             PRIMARY KEY  (id),
             UNIQUE KEY student_assignment (student_id, assignment_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_questions
         $table_name = $wpdb->prefix . 'mtts_questions';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             course_id mediumint(9) NOT NULL,
             question_text longtext NOT NULL,
             option_a text NOT NULL,
             option_b text NOT NULL,
             option_c text DEFAULT '',
             option_d text DEFAULT '',
             correct_option varchar(1) NOT NULL, -- a, b, c, d
             points int(3) DEFAULT 1,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_exam_results
         $table_name = $wpdb->prefix . 'mtts_exam_results';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             student_id mediumint(9) NOT NULL,
             course_id mediumint(9) NOT NULL,
             session_id mediumint(9) NOT NULL,
             score float DEFAULT 0,
             total_questions int(5) DEFAULT 0,
             answers longtext DEFAULT '', -- JSON store of student answers
             status varchar(20) DEFAULT 'started', -- started, submitted
             started_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_wallets
         $table_name = $wpdb->prefix . 'mtts_wallets';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             student_id mediumint(9) NOT NULL UNIQUE,
             balance decimal(10,2) DEFAULT 0.00,
             updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_wallet_transactions
         $table_name = $wpdb->prefix . 'mtts_wallet_transactions';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             wallet_id mediumint(9) NOT NULL,
             type varchar(20) NOT NULL, -- credit, debit
             amount decimal(10,2) NOT NULL,
             description varchar(255) DEFAULT '',
             reference varchar(100) DEFAULT '',
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_events (Academic Calendar)
         $table_name = $wpdb->prefix . 'mtts_events';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             title varchar(255) NOT NULL,
             start_date date NOT NULL,
             end_date date DEFAULT NULL,
             description text DEFAULT '',
             type varchar(50) DEFAULT 'general', -- holiday, exam, resumption, deadline
             session_id mediumint(9) NOT NULL,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_bonus_marks
         $table_name = $wpdb->prefix . 'mtts_bonus_marks';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             student_id mediumint(9) NOT NULL,
             course_id mediumint(9) NOT NULL,
             lecturer_id bigint(20) NOT NULL,
             marks float NOT NULL,
             reason varchar(255) DEFAULT '',
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_friend_requests
         $table_name = $wpdb->prefix . 'mtts_friend_requests';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             sender_id bigint(20) NOT NULL,
             receiver_id bigint(20) NOT NULL,
             status varchar(20) DEFAULT 'pending',
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             UNIQUE KEY unique_request (sender_id, receiver_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_messages
         $table_name = $wpdb->prefix . 'mtts_messages';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             sender_id bigint(20) NOT NULL,
             receiver_id bigint(20) NOT NULL,
             subject varchar(255) DEFAULT '',
             body longtext NOT NULL,
             is_read tinyint(1) DEFAULT 0,
             parent_id mediumint(9) DEFAULT NULL,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             KEY sender_id (sender_id),
             KEY receiver_id (receiver_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_forum_posts
         $table_name = $wpdb->prefix . 'mtts_forum_posts';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             author_id bigint(20) NOT NULL,
             course_id mediumint(9) DEFAULT NULL,
             category varchar(50) DEFAULT 'general',
             title varchar(255) NOT NULL,
             body longtext NOT NULL,
             is_pinned tinyint(1) DEFAULT 0,
             is_flagged tinyint(1) DEFAULT 0,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             KEY author_id (author_id),
             KEY course_id (course_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_forum_replies
         $table_name = $wpdb->prefix . 'mtts_forum_replies';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             post_id mediumint(9) NOT NULL,
             author_id bigint(20) NOT NULL,
             body longtext NOT NULL,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             KEY post_id (post_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_resources
         $table_name = $wpdb->prefix . 'mtts_resources';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             title varchar(255) NOT NULL,
             description text DEFAULT '',
             type varchar(20) DEFAULT 'pdf',
             url varchar(500) NOT NULL,
             course_id mediumint(9) DEFAULT NULL,
             uploaded_by bigint(20) NOT NULL,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             KEY course_id (course_id),
             KEY uploaded_by (uploaded_by)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_badges
         $table_name = $wpdb->prefix . 'mtts_badges';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             name varchar(100) NOT NULL,
             description varchar(255) DEFAULT '',
             icon varchar(10) DEFAULT '🏅',
             trigger_event varchar(100) DEFAULT '',
             trigger_value int DEFAULT 0,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Seed default badges
         $badge_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}mtts_badges" );
         if ( $badge_count == 0 ) {
             $default_badges = [
                 [ 'Academic Excellence', 'Scored 80% or above in an exam', '🌟', 'exam_score_80', 80 ],
                 [ 'Perfect Attendance', '100% attendance in a semester', '📅', 'attendance_100', 100 ],
                 [ 'Forum Champion', 'Posted 50 times in the discussion forum', '💬', 'forum_posts_50', 50 ],
                 [ 'Early Payer', 'Paid fees before the deadline', '💳', 'early_payment', 1 ],
                 [ 'Active Alumni', 'Connected with 10 alumni friends', '🤝', 'alumni_friends_10', 10 ],
             ];
             foreach ( $default_badges as $b ) {
                 $wpdb->insert( $wpdb->prefix . 'mtts_badges', [
                     'name' => $b[0], 'description' => $b[1], 'icon' => $b[2],
                     'trigger_event' => $b[3], 'trigger_value' => $b[4]
                 ] );
             }
         }

         // Table: mtts_user_badges
         $table_name = $wpdb->prefix . 'mtts_user_badges';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             user_id bigint(20) NOT NULL,
             badge_id mediumint(9) NOT NULL,
             awarded_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             UNIQUE KEY user_badge (user_id, badge_id)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_forms
         $table_name = $wpdb->prefix . 'mtts_forms';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             title varchar(255) NOT NULL,
             form_slug varchar(100) NOT NULL,
             form_data longtext NOT NULL,
             is_active tinyint(1) DEFAULT 1,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             UNIQUE KEY form_slug (form_slug)
         ) $charset_collate;";
         dbDelta( $sql );

         // Table: mtts_form_entries
         $table_name = $wpdb->prefix . 'mtts_form_entries';
         $sql = "CREATE TABLE $table_name (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             form_id mediumint(9) NOT NULL,
             user_id bigint(20) DEFAULT NULL,
             entry_data longtext NOT NULL,
             created_at datetime DEFAULT CURRENT_TIMESTAMP,
             PRIMARY KEY  (id),
             KEY form_id (form_id)
         ) $charset_collate;";
         dbDelta( $sql );
        // Table: mtts_alumni_posts (Ministry Feed)
        $table_name = $wpdb->prefix . 'mtts_alumni_posts';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            author_id bigint(20) NOT NULL,
            content longtext NOT NULL,
            type varchar(20) DEFAULT 'social', -- social, professional, job_opening
            job_details longtext DEFAULT NULL, -- JSON for LinkedIn-style job info
            media_url varchar(500) DEFAULT NULL,
            likes_count int DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY author_id (author_id)
        ) $charset_collate;";
        dbDelta( $sql );

        // Table: mtts_alumni_profiles
        $table_name = $wpdb->prefix . 'mtts_alumni_profiles';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL UNIQUE,
            headline varchar(255) DEFAULT '',
            current_ministry varchar(255) DEFAULT '',
            gifts_graces text DEFAULT '', -- Theological skills
            ministry_milestones longtext DEFAULT '', -- Timeline of appointments
            skills text DEFAULT '', -- Comma separated or JSON
            experience longtext DEFAULT '', -- JSON list of past roles
            bio text DEFAULT '',
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta( $sql );
        
        // Table: mtts_alumni_comments (Discernment)
        $table_name = $wpdb->prefix . 'mtts_alumni_comments';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            author_id bigint(20) NOT NULL,
            content text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) $charset_collate;";
        dbDelta( $sql );
    }
}
