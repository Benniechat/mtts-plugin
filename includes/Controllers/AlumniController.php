<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AlumniController {

    public static function init() {
        add_shortcode( 'mtts_alumni_dashboard', array( __CLASS__, 'render_dashboard' ) );

        // Register AJAX handlers for logged-in users
        add_action( 'wp_ajax_mtts_alumni_action', array( __CLASS__, 'handle_ajax' ) );
    }

    public static function render_dashboard() {
        $user_id  = get_current_user_id();
        $is_guest = ! $user_id;
        $user     = $user_id ? wp_get_current_user() : null;
        $view     = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'feed';

        // Restrict views for guests
        $guest_allowed_views = array( 'feed', 'directory', 'events', 'jobs' );
        if ( $is_guest && ! in_array( $view, $guest_allowed_views, true ) ) {
            $view = 'registration_cta';
        }

        ob_start();

        $titles = array(
            'overview'     => array('title' => 'Connect+ Overview',          'subtitle' => 'Global view of the seminary community.'),
            'feed'         => array('title' => 'MTTS Connect+',              'subtitle' => 'Stay connected with the latest ministry activities.'),
            'directory'    => array('title' => 'Connect+ Directory',         'subtitle' => 'Browse and connect with fellow community members.'),
            'profile'      => array('title' => 'Covenant Identity',          'subtitle' => 'View your public ministerial profile.'),
            'profile-edit' => array('title' => 'Refine Identity',            'subtitle' => 'Manage your profile and personal details.'),
            'messenger'    => array('title' => 'Direct Propagation',         'subtitle' => 'Secure communication for ministry collaboration.'),
            'groups'       => array('title' => 'Ministry Circles',           'subtitle' => 'Join and engage in specialized ministry groups.'),
            'friends'      => array('title' => 'Fellowship Circle',          'subtitle' => 'Manage your network of close ministry colleagues.'),
            'events'       => array('title' => 'Academic & Alumni Calendar', 'subtitle' => 'Stay updated with upcoming school and network events.'),
            'jobs'         => array('title' => 'Ministry Opportunities',     'subtitle' => 'Explore vocational and ministry positions.'),
            'security'     => array('title' => 'Security Settings',         'subtitle' => 'Update your community account password.'),
            'registration_cta' => array('title' => 'Join the Community',    'subtitle' => 'Become a part of the Mountain-Top Alumni Network.'),
        );

        $current_title = isset($titles[$view]) ? $titles[$view] : array('title' => 'MTTS Connect+', 'subtitle' => '');
        $page_title    = $current_title['title'];
        $page_subtitle = $current_title['subtitle'];

        $sidebar_path = MTTS_LMS_PATH . 'includes/Views/Alumni/sidebar.php';

        ob_start();

        // ---------------------------------------------------------------------------
        // Handle form POST actions (page-reload path) — fully secured
        // ---------------------------------------------------------------------------
        if (
            isset( $_POST['mtts_alumni_action'] ) &&
            is_user_logged_in() &&
            \MttsLms\Core\Security::check_request( 'mtts_alumni_social' )
        ) {
            self::handle_social_actions( $user );
        }

        echo '<div class="mtts-dashboard-inner-container">';

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
            case 'security':
                if ( ! $is_guest ) {
                    \MttsLms\Controllers\Student\StudentDashboardController::render_change_password();
                }
                break;
            case 'registration_cta':
                self::render_registration_cta();
                break;
            default:
                self::render_feed();
                break;
        }

        echo '</div>'; // .mtts-dashboard-inner-container
        $lms_content = ob_get_clean();

        $wrapper_class = 'lms-facebook-theme';
        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/Shared/lms-layout.php';
        return ob_get_clean();
    }

    // ---------------------------------------------------------------------------
    // AJAX endpoint (wp_ajax_mtts_alumni_action)
    // ---------------------------------------------------------------------------
    public static function handle_ajax() {
        // 1. Must be logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( array( 'message' => 'Unauthenticated.' ), 401 );
        }

        // 2. Verify nonce (sent as _wpnonce in every JS call)
        if (
            empty( $_POST['_wpnonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'mtts_alumni_social' )
        ) {
            wp_send_json_error( array( 'message' => 'Security check failed.' ), 403 );
        }

        // 3. User must have the alumni or student capability (or be admin)
        if (
            ! current_user_can( 'read' ) // every registered user
        ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
        }

        $user = wp_get_current_user();
        self::handle_social_actions( $user );
        wp_die(); // handle_social_actions exits via wp_send_json_* in AJAX mode
    }

    // ---------------------------------------------------------------------------
    // Central social-action dispatcher (POST & AJAX)
    // ---------------------------------------------------------------------------
    private static function handle_social_actions( $user ) {
        // Deep-sanitize all $_POST data for NoSQL / operator injection protection
        $post_data = \MttsLms\Core\Security::sanitize_deep( $_POST );
        $action    = sanitize_key( $post_data['mtts_alumni_action'] ?? '' );

        switch ( $action ) {

            // ── Create Post ────────────────────────────────────────────────────
            case 'create_post': {
                // Must be own action — already covered by nonce
                $content    = wp_kses_post( $_POST['content'] ?? '' );
                $type       = sanitize_key( $_POST['type'] ?? 'social' );
                $media_url  = '';
                $media_type = 'text';

                if ( ! empty( $_FILES['media_file']['name'] ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    $uploaded   = $_FILES['media_file'];
                    $is_video   = strpos( $uploaded['type'], 'video' ) !== false;
                    $max_size   = $is_video ? 10 * 1024 * 1024 : 2 * 1024 * 1024;

                    if ( $uploaded['size'] > $max_size ) {
                        if ( wp_doing_ajax() ) { wp_send_json_error( 'File too large.' ); }
                        wp_die( 'File too large.' );
                    }

                    // Whitelist allowed MIME types
                    $allowed_types = array(
                        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                        'video/mp4', 'video/quicktime', 'video/webm',
                    );
                    if ( ! in_array( $uploaded['type'], $allowed_types, true ) ) {
                        if ( wp_doing_ajax() ) { wp_send_json_error( 'File type not allowed.' ); }
                        wp_die( 'File type not allowed.' );
                    }

                    $movefile = wp_handle_upload( $uploaded, array( 'test_form' => false ) );
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $media_url  = $movefile['url'];
                        $media_type = $is_video ? 'video' : 'image';
                    }
                }

                if ( ! empty( $content ) || ! empty( $media_url ) ) {
                    \MttsLms\Models\AlumniPost::create( array(
                        'author_id'  => $user->ID,
                        'content'    => $content,
                        'type'       => $type,
                        'media_url'  => $media_url,
                        'media_type' => $media_type,
                    ) );
                }
                if ( wp_doing_ajax() ) {
                    wp_send_json_success( array( 'message' => 'Post created!' ) );
                }
                break;
            }

            // ── Update Profile ─────────────────────────────────────────────────
            case 'update_profile': {
                $update_data = array(
                    'headline'            => sanitize_text_field( $_POST['headline']            ?? '' ),
                    'current_ministry'    => sanitize_text_field( $_POST['current_ministry']    ?? '' ),
                    'location'            => sanitize_text_field( $_POST['location']            ?? '' ),
                    'interests'           => sanitize_text_field( $_POST['interests']           ?? '' ),
                    'gifts_graces'        => sanitize_text_field( $_POST['gifts_graces']        ?? '' ),
                    'ministry_milestones' => sanitize_textarea_field( $_POST['ministry_milestones'] ?? '' ),
                    'bio'                 => wp_kses_post( $_POST['bio'] ?? '' ),
                    'skills'              => sanitize_text_field( $_POST['skills']              ?? '' ),
                    'experience'          => sanitize_textarea_field( $_POST['experience']      ?? '' ),
                );

                require_once( ABSPATH . 'wp-admin/includes/file.php' );
                foreach ( array( 'profile_pic', 'banner_pic' ) as $field ) {
                    if ( ! empty( $_FILES[ $field ]['name'] ) ) {
                        // Whitelist image types only for profile pictures
                        $allowed_img = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
                        if ( ! in_array( $_FILES[ $field ]['type'], $allowed_img, true ) ) {
                            continue;
                        }
                        $movefile = wp_handle_upload( $_FILES[ $field ], array( 'test_form' => false ) );
                        if ( $movefile && ! isset( $movefile['error'] ) ) {
                            $db_field = ( $field === 'profile_pic' ) ? 'profile_picture_url' : 'banner_url';
                            $update_data[ $db_field ] = esc_url_raw( $movefile['url'] );
                        }
                    }
                }

                \MttsLms\Models\AlumniProfile::update_profile( $user->ID, $update_data );
                if ( wp_doing_ajax() ) {
                    wp_send_json_success( array( 'message' => 'Profile updated!' ) );
                }
                break;
            }

            // ── Amen / Like Post ───────────────────────────────────────────────
            case 'amen_post': {
                $post_id = absint( $_POST['post_id'] ?? 0 );
                if ( ! $post_id ) { wp_send_json_error( 'Invalid post.' ); }

                // Prevent double-amen in session via transient
                $transient_key = 'mtts_amen_' . $user->ID . '_' . $post_id;
                if ( get_transient( $transient_key ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Already amened.' ); }
                    break;
                }
                set_transient( $transient_key, 1, HOUR_IN_SECONDS );

                \MttsLms\Models\AlumniPost::like_post( $post_id );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'label' => 'Amened' ) ); }
                break;
            }

            // ── Amen Comment ───────────────────────────────────────────────────
            case 'amen_comment': {
                $comment_id = absint( $_POST['comment_id'] ?? 0 );
                if ( ! $comment_id ) { wp_send_json_error( 'Invalid comment.' ); }
                \MttsLms\Models\AlumniComment::like_comment( $comment_id );
                if ( wp_doing_ajax() ) { wp_send_json_success(); }
                break;
            }

            // ── Propagate / Share Post ─────────────────────────────────────────
            case 'propagate_post': {
                $post_id = absint( $_POST['post_id'] ?? 0 );
                if ( ! $post_id ) { wp_send_json_error( 'Invalid post.' ); }
                \MttsLms\Models\AlumniPost::propagate( $post_id, $user->ID );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Post Propagated!' ) ); }
                break;
            }

            // ── Private Message ────────────────────────────────────────────────
            case 'send_private_message': {
                $receiver_id = absint( $_POST['receiver_id'] ?? 0 );
                $body        = sanitize_textarea_field( $_POST['body'] ?? '' );

                if ( ! $receiver_id || empty( $body ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Missing fields.' ); }
                    break;
                }
                // Prevent sending to self
                if ( $receiver_id === $user->ID ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Cannot message yourself.' ); }
                    break;
                }
                // Verify receiver is a real user
                if ( ! get_userdata( $receiver_id ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Unknown user.' ); }
                    break;
                }
                \MttsLms\Models\Message::send( $user->ID, $receiver_id, 'Private Message', $body );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Encrypted message sent!' ) ); }
                break;
            }

            // ── Create Group ───────────────────────────────────────────────────
            case 'create_group': {
                $name        = sanitize_text_field( $_POST['name']        ?? '' );
                $description = sanitize_textarea_field( $_POST['description'] ?? '' );
                $privacy     = in_array( $_POST['privacy'] ?? '', array( 'public', 'private' ), true )
                               ? $_POST['privacy'] : 'public';

                if ( empty( $name ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Group name required.' ); }
                    break;
                }
                \MttsLms\Models\Group::create_group( array(
                    'name'        => $name,
                    'description' => $description,
                    'privacy'     => $privacy,
                    'creator_id'  => $user->ID,
                ) );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Circle created!' ) ); }
                break;
            }

            // ── Join Group ─────────────────────────────────────────────────────
            case 'join_group': {
                $group_id = absint( $_POST['group_id'] ?? 0 );
                if ( ! $group_id ) { if ( wp_doing_ajax() ) { wp_send_json_error( 'Invalid group.' ); } break; }
                \MttsLms\Models\GroupMember::add_member( $group_id, $user->ID );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Joined Group!' ) ); }
                break;
            }

            // ── Send Connection Request ────────────────────────────────────────
            case 'send_friend_request': {
                $receiver_id = absint( $_POST['receiver_id'] ?? 0 );

                if ( ! $receiver_id || $receiver_id === $user->ID ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Invalid request.' ); }
                    break;
                }
                if ( ! get_userdata( $receiver_id ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Unknown user.' ); }
                    break;
                }
                // Rate-limit: 20 requests per hour per sender
                $rate_key = 'mtts_conn_rate_' . $user->ID;
                $rate_val = (int) get_transient( $rate_key );
                if ( $rate_val >= 20 ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Rate limit reached. Try again later.' ); }
                    break;
                }
                set_transient( $rate_key, $rate_val + 1, HOUR_IN_SECONDS );

                $result = \MttsLms\Models\FriendRequest::send_request( $user->ID, $receiver_id );
                if ( wp_doing_ajax() ) {
                    if ( $result ) {
                        wp_send_json_success( array( 'message' => 'Request Sent!' ) );
                    } else {
                        wp_send_json_error( array( 'message' => 'Request already exists.' ) );
                    }
                }
                break;
            }

            // ── Accept Connection Request ──────────────────────────────────────
            case 'accept_friend_request': {
                $request_id = absint( $_POST['request_id'] ?? 0 );
                if ( ! $request_id ) { if ( wp_doing_ajax() ) { wp_send_json_error( 'Invalid.' ); } break; }

                // Verify this request was sent TO the current user
                global $wpdb;
                $table = $wpdb->prefix . 'mtts_friend_requests';
                $req   = $wpdb->get_row( $wpdb->prepare(
                    "SELECT * FROM {$table} WHERE id = %d AND receiver_id = %d AND status = 'pending'",
                    $request_id, $user->ID
                ) );

                if ( ! $req ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Request not found or already processed.' ); }
                    break;
                }

                \MttsLms\Models\FriendRequest::accept_request( $request_id );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Request Accepted!' ) ); }
                break;
            }

            // ── Reject / Decline Connection Request ───────────────────────────
            case 'reject_friend_request': {
                $request_id = absint( $_POST['request_id'] ?? 0 );
                if ( ! $request_id ) { if ( wp_doing_ajax() ) { wp_send_json_error( 'Invalid.' ); } break; }

                // Verify ownership — only receiver OR sender can reject
                global $wpdb;
                $table = $wpdb->prefix . 'mtts_friend_requests';
                $req   = $wpdb->get_row( $wpdb->prepare(
                    "SELECT * FROM {$table} WHERE id = %d AND (receiver_id = %d OR sender_id = %d) AND status = 'pending'",
                    $request_id, $user->ID, $user->ID
                ) );

                if ( ! $req ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Request not found or already processed.' ); }
                    break;
                }

                \MttsLms\Models\FriendRequest::reject_request( $request_id );
                if ( wp_doing_ajax() ) { wp_send_json_success( array( 'message' => 'Request Declined.' ) ); }
                break;
            }

            // ── Add Comment ────────────────────────────────────────────────────
            case 'add_comment': {
                $post_id = absint( $_POST['post_id'] ?? 0 );
                $content = sanitize_textarea_field( $_POST['content'] ?? '' );

                if ( ! $post_id || empty( $content ) ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Missing fields.' ); }
                    break;
                }
                // Limit comment length
                if ( mb_strlen( $content ) > 1000 ) {
                    if ( wp_doing_ajax() ) { wp_send_json_error( 'Comment too long (max 1000 chars).' ); }
                    break;
                }

                \MttsLms\Models\AlumniComment::create( array(
                    'post_id'   => $post_id,
                    'author_id' => $user->ID,
                    'content'   => $content,
                ) );
                if ( wp_doing_ajax() ) { wp_send_json_success(); }
                break;
            }

            default:
                if ( wp_doing_ajax() ) { wp_send_json_error( 'Unknown action.' ); }
                break;
        }
    }

    // ---------------------------------------------------------------------------
    // View renderers
    // ---------------------------------------------------------------------------
    private static function render_overview( $user ) {
        include MTTS_LMS_PATH . 'includes/Views/Alumni/overview.php';
    }

    private static function render_directory() {
        $args = array(
            'role'    => 'mtts_alumni',
            'orderby' => 'user_registered',
            'order'   => 'DESC',
        );
        $alumni_query = new \WP_User_Query( $args );
        $alumni       = $alumni_query->get_results();
        include MTTS_LMS_PATH . 'includes/Views/Alumni/directory.php';
    }

    private static function render_events() {
        $events = array(
            (object)array( 'title' => 'Annual Alumni Homecoming',  'date' => '2026-11-15', 'location' => 'Main Auditorium',  'description' => 'Join us for a time of reunion and fellowship.' ),
            (object)array( 'title' => 'Leadership Seminar',        'date' => '2026-06-20', 'location' => 'Virtual (Zoom)',    'description' => 'Upskilling for ministry leadership in the digital age.' ),
        );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/events.php';
    }

    private static function render_jobs() {
        $jobs = array(
            (object)array( 'title' => 'Youth Pastor',    'org' => 'Grace Chapel, Lagos', 'type' => 'Full-time',  'posted' => '2 days ago' ),
            (object)array( 'title' => 'Music Director',  'org' => 'City Faith Church',   'type' => 'Part-time',  'posted' => '1 week ago' ),
        );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/jobs.php';
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
        $pending_requests = \MttsLms\Models\FriendRequest::get_pending_requests( $user->ID );
        $friends_data     = \MttsLms\Models\FriendRequest::get_friends( $user->ID );
        include MTTS_LMS_PATH . 'includes/Views/Alumni/friends.php';
    }

    private static function render_registration_cta() {
        $admission_url = home_url('/admission');
        ?>
        <div class="mtts-card" style="padding: 60px; text-align: center; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="font-size: 64px; margin-bottom: 24px;">🎓</div>
            <h2 style="font-size: 32px; font-weight: 800; color: #1a1a2e; margin: 0 0 12px;">Authentication Required</h2>
            <p style="font-size: 18px; color: #6b7280; max-width: 600px; margin: 0 auto 30px; line-height: 1.6;">
                The mountain-top is for everyone, but some areas of our network are reserved for registered alumni and students. 
                Keep track of your colleagues and collaborate for ministry by joining us.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center;">
                <a href="<?php echo esc_url( wp_login_url( home_url('/alumni-network') ) ); ?>" class="stitch-btn-primary" style="padding: 14px 30px; font-size: 16px;">Log In Now</a>
                <a href="<?php echo esc_url( $admission_url ); ?>" class="stitch-btn-outline" style="padding: 14px 30px; font-size: 16px;">Apply to Join</a>
            </div>
            <p style="margin-top: 24px; font-size: 14px; color: #9ca3af;">
                Registration is only through official application or admin onboarding.
            </p>
        </div>
        <?php
    }
}
