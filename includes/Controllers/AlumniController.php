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
        if ( ! is_user_logged_in() ) {
            return '<div class="mtts-alert mtts-alert-info">Please <a href="' . wp_login_url( get_permalink() ) . '">log in</a> to access the Alumni & Community Network.</div>';
        }

        $user = wp_get_current_user();
        $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'feed';

        ob_start();
        echo '<div class="mtts-dashboard-wrapper">';
        
        // GLOBAL TOP BAR - Facebook/LinkedIn Style
        $view_titles = [
            'overview' => 'Network Overview',
            'feed' => 'Ministerial Nexus',
            'directory' => 'Alumni Directory',
            'profile' => 'Professional Portal',
            'profile-edit' => 'Edit Covenant Identity',
            'messenger' => 'Propagate Messenger',
            'groups' => 'Ministry Circles',
            'friends' => 'Fellowship Circle',
            'events' => 'Academic & Alumni Calendar',
            'jobs' => 'Ministry Opportunities'
        ];
        $current_title = isset($view_titles[$view]) ? $view_titles[$view] : 'Alumni Hub';

        echo '<div class="mtts-dashboard-sticky-header koinonia-glass">';
        echo '  <div class="mtts-header-left">';
        echo '      <div class="mtts-school-logo-mini">';
        echo '          <span class="dashicons dashicons-welcome-learn-more"></span>';
        echo '      </div>';
        echo '      <h2 class="spiritual-gradient-text" style="font-size:1.2rem; margin:0;">' . esc_html($current_title) . '</h2>';
        echo '  </div>';
        echo '  <div class="mtts-header-right">';
        echo '      <span class="mtts-current-user">Koinonia: ' . esc_html($user->display_name) . '</span>';
        echo '  </div>';
        echo '</div>';

        // Sidebar
        include MTTS_LMS_PATH . 'includes/Views/Alumni/sidebar.php';

        // Handle Social & Professional Actions
        if ( isset( $_POST['mtts_alumni_action'] ) && \MttsLms\Core\Security::check_request( 'mtts_alumni_social' ) ) {
            self::handle_social_actions( $user );
        }

        echo '<div class="mtts-dashboard-content">';
        echo '<div class="mtts-dashboard-inner-container">';

        // Router
        switch ( $view ) {
            case 'feed':
                self::render_feed();
                break;
            case 'directory':
                self::render_directory();
                break;
            case 'overview':
                self::render_overview( $user );
                break;
            case 'groups':
                include MTTS_LMS_PATH . 'includes/Views/Alumni/groups.php';
                break;
            case 'profile':
                self::render_profile( $user );
                break;
            case 'profile-edit':
                self::render_profile_edit( $user );
                break;
            case 'messenger':
                include MTTS_LMS_PATH . 'includes/Views/Alumni/messenger.php';
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
            default:
                self::render_feed();
                break;
        }

        echo '</div>'; // End inner container
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
            $content    = wp_kses_post( $_POST['content'] );
            $type       = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'social';
            $media_url  = '';
            $media_type = 'text';

            // Handle Media Upload
            if ( ! empty( $_FILES['media_file']['name'] ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                $uploadedfile = $_FILES['media_file'];
                $upload_overrides = array( 'test_form' => false );
                
                // Constraints
                $max_size = strpos( $uploadedfile['type'], 'video' ) !== false ? 10 * 1024 * 1024 : 2 * 1024 * 1024;
                if ( $uploadedfile['size'] > $max_size ) {
                     if ( wp_doing_ajax() ) { wp_send_json_error( 'File too large' ); exit; }
                     wp_die( 'File too large.' );
                }

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    $media_url  = $movefile['url'];
                    $media_type = strpos( $uploadedfile['type'], 'video' ) !== false ? 'video' : 'image';
                }
            }
            
            if ( ! empty( $content ) || ! empty( $media_url ) ) {
                \MttsLms\Models\AlumniPost::create( [
                    'author_id'  => $user->ID,
                    'content'    => $content,
                    'type'       => $type,
                    'media_url'  => $media_url,
                    'media_type' => $media_type
                ] );
            }
        } elseif ( $action === 'update_profile' ) {
            $post_data = \MttsLms\Core\Security::sanitize_deep( $_POST );
            $update_data = [
                'headline'             => $post_data['headline'] ?? '',
                'current_ministry'     => $post_data['current_ministry'] ?? '',
                'location'             => $post_data['location'] ?? '',
                'interests'            => $post_data['interests'] ?? '',
                'gifts_graces'         => $post_data['gifts_graces'] ?? '',
                'ministry_milestones'  => $post_data['ministry_milestones'] ?? '',
                'bio'                  => wp_kses_post( $_POST['bio'] ?? '' ),
                'skills'               => $post_data['skills'] ?? '',
                'experience'           => $post_data['experience'] ?? '' 
            ];

            // Handle Multi-Media Profile Updates (Profile Pic & Banner)
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            foreach ( ['profile_pic', 'banner_pic'] as $field ) {
                if ( ! empty( $_FILES[$field]['name'] ) ) {
                    $movefile = wp_handle_upload( $_FILES[$field], array( 'test_form' => false ) );
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $db_field = ( $field === 'profile_pic' ) ? 'profile_picture_url' : 'banner_url';
                        $update_data[$db_field] = $movefile['url'];
                    }
                }
            }

            \MttsLms\Models\AlumniProfile::update_profile( $user->ID, $update_data );
        } elseif ( $action === 'amen_post' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $post_id = intval( $_POST['post_id'] );
            \MttsLms\Models\AlumniPost::like_post( $post_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'label' => 'Amened' ) );
                exit;
            }
        } elseif ( $action === 'amen_comment' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $comment_id = intval( $_POST['comment_id'] );
            \MttsLms\Models\AlumniComment::like_comment( $comment_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success();
                exit;
            }
        }
 elseif ( $action === 'propagate_post' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $post_id = intval( $_POST['post_id'] );
            // Logic to share/propagate post (usually creates a new post with reference)
            \MttsLms\Models\AlumniPost::propagate( $post_id, $user->ID );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'message' => 'Post Propagated!' ) );
                exit;
            }
        } elseif ( $action === 'send_private_message' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $receiver_id = intval( $_POST['receiver_id'] );
            $body        = sanitize_textarea_field( $_POST['body'] );
            
            if ( $receiver_id && ! empty( $body ) ) {
                \MttsLms\Models\Message::send( $user->ID, $receiver_id, 'Private Message', $body );
                if ( wp_doing_ajax() ) {
                    wp_send_json_success( array( 'message' => 'Encrypted message sent!' ) );
                    exit;
                }
            }
        } elseif ( $action === 'create_group' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            \MttsLms\Models\Group::create_group( array(
                'name'        => $_POST['name'],
                'description' => $_POST['description'],
                'privacy'     => $_POST['privacy'],
                'creator_id'  => $user->ID
            ) );
        } elseif ( $action === 'join_group' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $group_id = intval( $_POST['group_id'] );
            \MttsLms\Models\GroupMember::add_member( $group_id, $user->ID );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'message' => 'Joined Group!' ) );
                exit;
            }
        } elseif ( $action === 'send_friend_request' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $receiver_id = intval( $_POST['receiver_id'] );
            \MttsLms\Models\FriendRequest::send_request( $user->ID, $receiver_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'message' => 'Request Sent!' ) );
                exit;
            }
        } elseif ( $action === 'accept_friend_request' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $request_id = intval( $_POST['request_id'] );
            \MttsLms\Models\FriendRequest::accept_request( $request_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'message' => 'Request Accepted!' ) );
                exit;
            }
        } elseif ( $action === 'reject_friend_request' ) {
            \MttsLms\Core\Security::verify_nonce( 'mtts_alumni_social' );
            $request_id = intval( $_POST['request_id'] );
            \MttsLms\Models\FriendRequest::reject_request( $request_id );
            if ( wp_doing_ajax() ) {
                wp_send_json_success( array( 'message' => 'Request Rejected!' ) );
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
        $profile = \MttsLms\Models\AlumniProfile::get_by_user( $user->ID );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/profile.php';
    }

    private static function render_friends( $user ) {
        // Get pending requests
        $pending_requests = \MttsLms\Models\FriendRequest::get_pending_requests( $user->ID );
        
        // Get friends list
        $friends_data = \MttsLms\Models\FriendRequest::get_friends( $user->ID );
        
        include MTTS_LMS_PATH . 'includes/Views/Alumni/friends.php';
    }
}
