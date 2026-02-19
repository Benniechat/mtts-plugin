<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AlumniController {

    public static function init() {
        add_shortcode( 'mtts_alumni_dashboard', array( __CLASS__, 'render_dashboard' ) );
    }

    public static function render_dashboard() {
        if ( ! is_user_logged_in() ) { // In real app, check for 'alumni' role specifically
            return '<p>Please log in to access the Alumni Network.</p>';
        }

        $user = wp_get_current_user();
        if ( ! in_array( 'mtts_alumni', (array) $user->roles ) && ! current_user_can( 'manage_options' ) ) {
            return '<div class="mtts-alert mtts-alert-warning">Access restricted to Alumni only.</div>';
        }

        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'overview';

        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        
        // Sidebar
        include MTTS_LMS_PATH . 'includes/Views/Alumni/sidebar.php';

        // Handle Social & Professional Actions
        if ( isset( $_POST['mtts_alumni_action'] ) && \MttsLms\Core\Security::check_request( 'mtts_alumni_social' ) ) {
            self::handle_social_actions( $user );
        }

        echo '<div class="mtts-dashboard-content">';
        
        // Router
        switch ( $view ) {
            case 'feed':
                self::render_feed();
                break;
            case 'profile':
                self::render_profile_edit( $user );
                break;
            case 'portfolio':
                $student = \MttsLms\Models\Student::get_by_user( $user->ID );
                if ( $student ) {
                    echo \MttsLms\Controllers\PortfolioController::render_portfolio_view( $student );
                }
                break;
            case 'directory':
                self::render_directory();
                break;
            case 'friends':
                self::render_friends( $user );
                break;
            case 'events':
                self::render_events();
                break;
            case 'jobs':
                self::render_jobs();
                break;
            case 'overview':
            default:
                self::render_overview( $user );
                break;
        }

        echo '</div></div>';
        return ob_get_clean();
    }

    private static function render_overview( $user ) {
        include MTTS_LMS_PATH . 'includes/Views/Alumni/overview.php';
    }

    private static function render_directory() {
        // Fetch all alumni
        $args = array(
            'role'    => 'mtts_alumni',
            'orderby' => 'user_registered',
            'order'   => 'DESC'
        );
        $alumni_query = new \WP_User_Query( $args );
        $alumni = $alumni_query->get_results();

        include MTTS_LMS_PATH . 'includes/Views/Alumni/directory.php';
    }

    private static function render_events() {
        // Placeholder for events data
        $events = [
            (object)['title' => 'Annual Alumni Homecoming', 'date' => '2026-11-15', 'location' => 'Main Auditorium', 'description' => 'Join us for a time of reunion and fellowship.'],
            (object)['title' => 'Leadership Seminar', 'date' => '2026-06-20', 'location' => 'Virtual (Zoom)', 'description' => 'Upskilling for ministry leadership in the digital age.']
        ];
        include MTTS_LMS_PATH . 'includes/Views/Alumni/events.php';
    }

    private static function render_jobs() {
        // Placeholder for jobs data
        $jobs = [
            (object)['title' => 'Youth Pastor', 'org' => 'Grace Chapel, Lagos', 'type' => 'Full-time', 'posted' => '2 days ago'],
            (object)['title' => 'Music Director', 'org' => 'City Faith Church', 'type' => 'Part-time', 'posted' => '1 week ago']
        ];
        include MTTS_LMS_PATH . 'includes/Views/Alumni/jobs.php';
    }

    private static function handle_social_actions( $user ) {
        $action = sanitize_key( $_POST['mtts_alumni_action'] );

        if ( $action === 'create_post' ) {
            $content = wp_kses_post( $_POST['content'] ); // Allow basic HTML but strip scripts
            $type    = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'social';
            
            if ( ! empty( $content ) ) {
                \MttsLms\Models\AlumniPost::create( [
                    'author_id' => $user->ID,
                    'content'   => $content,
                    'type'      => $type
                ] );
            }
        } elseif ( $action === 'update_profile' ) {
            $post_data = \MttsLms\Core\Security::sanitize_deep( $_POST );
            \MttsLms\Models\AlumniProfile::update_profile( $user->ID, [
                'headline'             => $post_data['headline'] ?? '',
                'current_ministry'     => $post_data['current_ministry'] ?? '',
                'gifts_graces'         => $post_data['gifts_graces'] ?? '',
                'ministry_milestones'  => $post_data['ministry_milestones'] ?? '',
                'bio'                  => wp_kses_post( $_POST['bio'] ?? '' ), // Allow safe HTML in bio
                'skills'               => $post_data['skills'] ?? '',
                'experience'           => $post_data['experience'] ?? '' 
            ] );
        } elseif ( $action === 'like_post' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' ); // Additional check for AJAX
            $post_id = intval( $_POST['post_id'] );
            \MttsLms\Models\AlumniPost::like_post( $post_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success();
                exit;
            }
        } elseif ( $action === 'add_comment' ) {
            $post_id = intval( $_POST['post_id'] );
            $content = sanitize_textarea_field( $_POST['content'] );
            
            if ( ! empty( $content ) && $post_id ) {
                \MttsLms\Models\AlumniComment::create( [
                    'post_id'   => $post_id,
                    'author_id' => $user->ID,
                    'content'   => $content
                ] );
            }
            if ( wp_doing_ajax() ) {
                wp_send_json_success();
                exit;
            }
        }
    }

    private static function render_feed() {
        $posts = \MttsLms\Models\AlumniPost::get_feed( 20 );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/feed.php';
    }

    private static function render_profile_edit( $user ) {
        $profile = \MttsLms\Models\AlumniProfile::get_by_user( $user->ID );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/profile-edit.php';
    }

    private static function render_profile( $user ) {
        // reuse student profile logic or custom alumni profile
        // For now, simple view
        include MTTS_LMS_PATH . 'includes/Views/Alumni/profile.php';
    }

    private static function render_friends( $user ) {
        // Handle Friend Request Actions
        if ( isset( $_POST['mtts_action'] ) ) {
            if ( $_POST['mtts_action'] == 'send_request' && \MttsLms\Core\Security::check_request( 'mtts_send_friend_request' ) ) {
                $receiver_id = intval( $_POST['receiver_id'] );
                \MttsLms\Models\FriendRequest::send_request( $user->ID, $receiver_id );
                echo '<div class="mtts-alert mtts-alert-success">Friend request sent!</div>';
            } elseif ( $_POST['mtts_action'] == 'accept_request' && \MttsLms\Core\Security::check_request( 'mtts_accept_friend_request' ) ) {
                $request_id = intval( $_POST['request_id'] );
                \MttsLms\Models\FriendRequest::accept_request( $request_id );
                echo '<div class="mtts-alert mtts-alert-success">Friend request accepted!</div>';
            } elseif ( $_POST['mtts_action'] == 'reject_request' && \MttsLms\Core\Security::check_request( 'mtts_reject_friend_request' ) ) {
                $request_id = intval( $_POST['request_id'] );
                \MttsLms\Models\FriendRequest::reject_request( $request_id );
                echo '<div class="mtts-alert mtts-alert-info">Friend request rejected.</div>';
            }
        }

        // Get pending requests
        $pending_requests = \MttsLms\Models\FriendRequest::get_pending_requests( $user->ID );
        
        // Get friends list
        $friends_data = \MttsLms\Models\FriendRequest::get_friends( $user->ID );
        
        include MTTS_LMS_PATH . 'includes/Views/Alumni/friends.php';
    }
}
