<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JwtAuth {

    private static $secret_key;

    public static function init() {
        self::$secret_key = defined('MTTS_JWT_SECRET') ? MTTS_JWT_SECRET : wp_salt('auth');
        
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
        add_filter( 'rest_authentication_errors', array( __CLASS__, 'validate_token' ) );
    }

    public static function register_routes() {
        register_rest_route( 'mtts-lms/v1', '/auth/token', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'generate_token' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function generate_token( \WP_REST_Request $request ) {
        $username = $request->get_param( 'username' );
        $password = $request->get_param( 'password' );

        $user = wp_authenticate( $username, $password );

        if ( is_wp_error( $user ) ) {
            return new \WP_Error( 'invalid_credentials', 'Invalid username or password', array( 'status' => 403 ) );
        }

        $issuedAt = time();
        $expirationTime = $issuedAt + ( 24 * 60 * 60 ); // 1 day
        $payload = array(
            'iss' => get_bloginfo( 'url' ),
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => array(
                'user_id' => $user->ID,
                'roles' => $user->roles
            )
        );

        $token = self::encode( $payload );

        return array(
            'token' => $token,
            'user_email' => $user->user_email,
            'user_nicename' => $user->user_nicename,
            'user_display_name' => $user->display_name,
        );
    }

    public static function validate_token( $error ) {
        //If we already have an error, or if this is not an API request we care about (maybe?), return.
        //But actually we want to authenticate if the Authorization header is present.
        
        $header = self::get_authorization_header();
        
        if ( ! $header ) {
            return $error; // Let other auth methods handle it or return existing error
        }

        if ( strpos( $header, 'Bearer ' ) !== 0 ) {
            return $error;
        }

        $token = substr( $header, 7 );
        $payload = self::decode( $token );

        if ( ! $payload ) {
            return new \WP_Error( 'invalid_token', 'Invalid or expired token', array( 'status' => 401 ) );
        }

        // Set the current user
        wp_set_current_user( $payload['data']['user_id'] );
        return true;
    }

    private static function encode( $payload ) {
        $header = json_encode( array( 'typ' => 'JWT', 'alg' => 'HS256' ) );
        $payload = json_encode( $payload );

        $base64UrlHeader = self::base64UrlEncode( $header );
        $base64UrlPayload = self::base64UrlEncode( $payload );

        $signature = hash_hmac( 'sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true );
        $base64UrlSignature = self::base64UrlEncode( $signature );

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private static function decode( $token ) {
        $tokenParts = explode( '.', $token );
        if ( count( $tokenParts ) != 3 ) {
            return false;
        }

        $header = base64_decode( $tokenParts[0] );
        $payload = base64_decode( $tokenParts[1] );
        $signature_provided = $tokenParts[2];

        // check expiration
        $payload_obj = json_decode( $payload, true );
        if ( isset( $payload_obj['exp'] ) && $payload_obj['exp'] < time() ) {
            return false;
        }

        // check signature
        $base64UrlHeader = self::base64UrlEncode( $header );
        $base64UrlPayload = self::base64UrlEncode( $payload );
        $signature = hash_hmac( 'sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret_key, true );
        $base64UrlSignature = self::base64UrlEncode( $signature );

        if ( $base64UrlSignature === $signature_provided ) {
            return $payload_obj;
        }

        return false;
    }

    private static function base64UrlEncode( $text ) {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode( $text )
        );
    }

    private static function get_authorization_header() {
        $headers = null;
        if ( isset( $_SERVER['Authorization'] ) ) {
            $headers = trim( $_SERVER["Authorization"] );
        } else if ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) { //Nginx or fast CGI
            $headers = trim( $_SERVER["HTTP_AUTHORIZATION"] );
        } elseif ( function_exists( 'apache_request_headers' ) ) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if ( isset( $requestHeaders['Authorization'] ) ) {
                $headers = trim( $requestHeaders['Authorization'] );
            }
        }
        return $headers;
    }
}
