<?php
namespace MttsLms\Core;

use MttsLms\Models\Student;
use MttsLms\Models\Program;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ShortcodeBank {

    public static function init() {
        $shortcodes = array(
            'mtts_user_name'    => 'get_user_name',
            'mtts_user_email'   => 'get_user_email',
            'mtts_user_matric'  => 'get_user_matric',
            'mtts_user_program' => 'get_user_program',
            'mtts_user_level'   => 'get_user_level',
            'mtts_today'            => 'get_today_date',
            'mtts_site_name'        => 'get_site_name',
            'mtts_portal_url'       => 'get_portal_url',
            'mtts_alumni_url'       => 'get_alumni_url',
            'mtts_stakeholder_url'  => 'get_stakeholder_url',
            'mtts_admission_url'    => 'get_admission_url',
            'mtts_dashboard_url'    => 'get_dashboard_url',
            'mtts_logout_url'       => 'get_logout_url',
            'mtts_portal_link'      => 'get_portal_link',
            'mtts_alumni_link'      => 'get_alumni_link',
            'mtts_stakeholder_link' => 'get_stakeholder_link',
            'mtts_admission_link'   => 'get_admission_link',
            'mtts_dashboard_link'   => 'get_dashboard_link',
            'mtts_logout_link'      => 'get_logout_link',
            'mtts_id_card'          => 'get_id_card',
            'mtts_exam_results'     => 'get_exam_results',
            'mtts_study_materials'  => 'get_study_materials',
            'mtts_assignments'      => 'get_assignments',
            'mtts_calendar'         => 'get_calendar',
            'mtts_wallet'           => 'get_wallet',
            'mtts_badges'           => 'get_badges',
            'mtts_student_profile'  => 'get_student_profile',
            'mtts_inquiry_form'     => 'get_inquiry_form',
            'mtts_noticeboard'      => 'get_noticeboard',
            'mtts_alumni_directory' => 'get_alumni_directory',
            'mtts_translator'       => 'render_translator',
        );

        foreach ( $shortcodes as $tag => $callback ) {
            add_shortcode( $tag, array( __CLASS__, $callback ) );
        }
    }

    public static function get_user_name() {
        $user = wp_get_current_user();
        return $user->exists() ? $user->display_name : '';
    }

    public static function get_user_email() {
        $user = wp_get_current_user();
        return $user->exists() ? $user->user_email : '';
    }

    public static function get_user_matric() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) return '';

        $student = Student::get_by_user( $user_id );
        return $student ? $student->matric_number : '';
    }

    public static function get_user_program() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) return '';

        $student = Student::get_by_user( $user_id );
        if ( ! $student || ! $student->program_id ) return '';

        $program = Program::find( $student->program_id );
        return $program ? $program->name : '';
    }

    public static function get_user_level() {
        $user_id = get_current_user_id();
        if ( ! $user_id ) return '';

        $student = Student::get_by_user( $user_id );
        return $student ? $student->current_level : '';
    }

    public static function get_today_date() {
        return date_i18n( get_option( 'date_format' ) );
    }

    public static function get_site_name() {
        return get_bloginfo( 'name' );
    }

    // --- Navigation URLs ---

    public static function get_portal_url() {
        return home_url( '/portal-login' );
    }

    public static function get_alumni_url() {
        return home_url( '/alumni-network' );
    }

    public static function get_stakeholder_url() {
        return home_url( '/stakeholder-login' );
    }

    public static function get_admission_url() {
        return home_url( '/admission' );
    }

    public static function get_dashboard_url() {
        $user = wp_get_current_user();
        if ( ! $user->exists() ) return self::get_portal_url();

        if ( in_array( 'mtts_student', (array) $user->roles ) ) {
            return home_url( '/student-dashboard' );
        } elseif ( in_array( 'mtts_lecturer', (array) $user->roles ) ) {
            return home_url( '/lecturer-dashboard' );
        } elseif ( in_array( 'mtts_alumni', (array) $user->roles ) ) {
            return home_url( '/alumni-network' );
        } elseif ( in_array( 'administrator', (array) $user->roles ) || in_array( 'mtts_school_admin', (array) $user->roles ) ) {
            return admin_url();
        }
        return home_url();
    }

    public static function get_logout_url() {
        return wp_logout_url( home_url() );
    }

    // --- Navigation Links (HTML) ---

    public static function get_portal_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Portal Login', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_portal_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    public static function get_alumni_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Alumni Network', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_alumni_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    public static function get_stakeholder_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Stakeholder Login', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_stakeholder_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    public static function get_admission_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Apply Now', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_admission_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    public static function get_dashboard_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'My Dashboard', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_dashboard_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    public static function get_logout_link( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Logout', 'class' => 'mtts-link' ), $atts );
        return sprintf( '<a href="%s" class="%s">%s</a>', esc_url( self::get_logout_url() ), esc_attr( $atts['class'] ), esc_html( $atts['text'] ) );
    }

    // --- Feature Components ---

    public static function get_id_card() {
        return self::render_student_component( 'id-card' );
    }

    public static function get_exam_results() {
        return self::render_student_component( 'exam-results' );
    }

    public static function get_study_materials() {
        return self::render_student_component( 'resources' );
    }

    public static function get_assignments() {
        return self::render_student_component( 'assignments' );
    }

    public static function get_calendar() {
        return self::render_student_component( 'calendar' );
    }

    public static function get_wallet() {
        return self::render_student_component( 'wallet' );
    }

    public static function get_badges() {
        return self::render_student_component( 'badges' );
    }

    public static function get_student_profile() {
        return self::render_student_component( 'profile' );
    }

    private static function render_student_component( $view ) {
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-warning">Please <a href="' . esc_url( self::get_portal_url() ) . '">log in</a> to view this content.</div>';
        }

        $user_id = get_current_user_id();
        $student = Student::get_by_user( $user_id );

        if ( ! $student ) {
            return '<div class="mtts-alert mtts-alert-danger">Student record not found.</div>';
        }

        // Buffer the output from the specific view
        ob_start();
        $view_path = MTTS_LMS_PATH . "includes/Views/Student/{$view}.php";
        if ( file_exists( $view_path ) ) {
            // Some views might need extra logic from controllers. 
            // For now, we include the view file which usually expects $student to be defined.
            include $view_path;
        } else {
            echo "Component [{$view}] not found.";
        }
        return ob_get_clean();
    }

    public static function get_alumni_directory() {
        ob_start();
        \MttsLms\Controllers\AlumniController::render_directory();
        return ob_get_clean();
    }

    public static function get_noticeboard() {
        $events = \MttsLms\Models\Event::get_upcoming( 5 );
        
        ob_start();
        ?>
        <div class="mtts-noticeboard-container">
            <h3 class="mtts-section-title">Noticeboard</h3>
            <?php if ( empty( $events ) ) : ?>
                <p>No recent notices.</p>
            <?php else : ?>
                <div class="mtts-notices-list">
                    <?php foreach ( $events as $event ) : ?>
                        <div class="mtts-notice-card" style="margin-bottom: 15px; padding: 15px; background: rgba(255,255,255,0.05); border-left: 4px solid #7c3aed; backdrop-filter: blur(10px); border-radius: 8px;">
                            <div class="mtts-notice-date" style="font-size: 0.8em; opacity: 0.7; color: #fff;">
                                <?php echo date_i18n( get_option( 'date_format' ), strtotime( $event->start_date ) ); ?>
                            </div>
                            <h4 style="margin: 5px 0; color: #fff;"><?php echo esc_html( $event->title ); ?></h4>
                            <p style="margin: 0; font-size: 0.9em; opacity: 0.8; color: #fff;"><?php echo esc_html( $event->description ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function get_inquiry_form() {
        // Handle Inquiry Submission
        if ( isset( $_POST['mtts_inquiry_submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_inquiry_action' ) ) {
            $name    = sanitize_text_field( $_POST['full_name'] );
            $email   = sanitize_email( $_POST['email'] );
            $message = sanitize_textarea_field( $_POST['message'] );

            // Send notification to admin
            $admin_email = get_option( 'admin_email' );
            $subject     = "New Admission Inquiry from {$name}";
            $body        = "<strong>Name:</strong> {$name}<br><strong>Email:</strong> {$email}<br><br><strong>Message:</strong><br>{$message}";
            
            \MttsLms\Core\NotificationManager::send_email( $admin_email, $subject, $body );

            return '<div class="mtts-alert mtts-alert-success">Thank you for your inquiry. We will get back to you soon!</div>';
        }

        ob_start();
        ?>
        <div class="mtts-inquiry-box" style="padding: 25px; background: rgba(255,255,255,0.1); border-radius: 12px; backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1); color: #fff;">
            <h3>Admission Inquiry</h3>
            <form method="post" action="">
                <?php wp_nonce_field( 'mtts_inquiry_action' ); ?>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Full Name</label>
                    <input type="text" name="full_name" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 5px; color: #fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">Email Address</label>
                    <input type="email" name="email" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 5px; color: #fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px;">How can we help you?</label>
                    <textarea name="message" rows="4" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 5px; color: #fff;"></textarea>
                </div>
                <button type="submit" name="mtts_inquiry_submit" style="background: #7c3aed; color: #fff; border: 0; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-weight: 600;">Send Inquiry</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function render_translator() {
        ob_start();
        \MttsLms\Core\Translator::render_google_translate_widget();
        return ob_get_clean();
    }
}
