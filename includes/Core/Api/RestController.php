<?php
namespace MttsLms\Core\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use MttsLms\Core\JwtAuth;

class RestController extends \WP_REST_Controller {

    protected $namespace = 'mtts/v1';

    public static function init() {
        $self = new self();
        add_action( 'rest_api_init', array( $self, 'register_routes' ) );
    }

    public function register_routes() {
        // Auth
        register_rest_route( $this->namespace, '/auth/login', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => array( $this, 'login' ),
            'permission_callback' => '__return_true',
        ) );

        // Student Profile
        register_rest_route( $this->namespace, '/student/profile', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_profile' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );
        
        // Student Courses
        register_rest_route( $this->namespace, '/student/courses', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_courses' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Student Results
        register_rest_route( $this->namespace, '/student/results', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_results' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );

        // Student Payments
        register_rest_route( $this->namespace, '/student/payments', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_payments' ),
            'permission_callback' => array( $this, 'check_auth' ),
        ) );
    }

    public function check_auth( $request ) {
        // Implement JWT checking here
        // For MVP, checking if WP user is logged in via cookie OR standard Basic Auth
        // Proper JWT validation would happen here using JwtAuth class created earlier
        return is_user_logged_in(); // Placeholder
    }

    public function login( $request ) {
        $params = $request->get_json_params();
        $username = $params['username'];
        $password = $params['password'];

        $user = wp_authenticate( $username, $password );

        if ( is_wp_error( $user ) ) {
            return new \WP_Error( 'rest_forbidden', 'Invalid credentials.', array( 'status' => 403 ) );
        }

        // Generate Token (mock for now if JwtAuth is not fully fleshed out)
        $token = JwtAuth::generate_token( $user ); 

        return rest_ensure_response( array(
            'token' => $token,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name
        ) );
    }

    public function get_profile( $request ) {
        $user_id = get_current_user_id();
        global $wpdb;
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mtts_students WHERE user_id = %d", $user_id ) );

        if ( ! $student ) {
            return new \WP_Error( 'not_found', 'Student profile not found.', array( 'status' => 404 ) );
        }

        return rest_ensure_response( $student );
    }
    
    public function get_courses( $request ) {
        $user_id = get_current_user_id();
        global $wpdb;
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mtts_students WHERE user_id = %d", $user_id ) );
        
        if ( ! $student ) return [];

        $registrations = \MttsLms\Models\Registration::get_student_courses( $student->id );
        return rest_ensure_response( $registrations );
    }

    public function get_results( $request ) {
        $user_id = get_current_user_id();
        global $wpdb;
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mtts_students WHERE user_id = %d", $user_id ) );
        
        if ( ! $student ) return [];

        $table = $wpdb->prefix . 'mtts_exam_results';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE student_id = %d ORDER BY submitted_at DESC", $student->id ) );
        
        return rest_ensure_response( $results );
    }

    public function get_payments( $request ) {
        // Placeholder for payment history
        return rest_ensure_response( [] );
    }
}
