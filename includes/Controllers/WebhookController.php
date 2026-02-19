<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WebhookController {

    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_webhook_routes' ) );
    }

    public static function register_webhook_routes() {
        register_rest_route( 'mtts/v1', '/webhook/paystack', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'handle_paystack' ),
            'permission_callback' => '__return_true', // Webhooks are public but validated by signature
        ) );

        register_rest_route( 'mtts/v1', '/webhook/flutterwave', array(
            'methods' => 'POST',
            'callback' => array( __CLASS__, 'handle_flutterwave' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function handle_paystack( $request ) {
        $body = $request->get_body();
        $signature = $request->get_header( 'x-paystack-signature' );
        $secret = get_option( 'mtts_paystack_secret_key' );

        if ( ! $secret || $signature !== hash_hmac( 'sha512', $body, $secret ) ) {
            return new \WP_Error( 'invalid_signature', 'Invalid Signature', array( 'status' => 401 ) );
        }

        $event = json_decode( $body );

        if ( 'charge.success' === $event->event ) {
            $reference = $event->data->reference;
            \MttsLms\Controllers\Student\PaymentController::verify_transaction( $reference );
            return rest_ensure_response( array( 'status' => 'success' ) );
        }

        return rest_ensure_response( array( 'status' => 'ignored' ) );
    }

    public static function handle_flutterwave( $request ) {
        $body = $request->get_body();
        $signature = $request->get_header( 'verif-hash' );
        $secret = get_option( 'mtts_flutterwave_secret_key' );
        // Flutterwave signature logic might vary, usually set in dashboard
        
        $event = json_decode( $body );
        if ( 'charge.completed' === $event->event && $event->data->status === 'successful' ) {
            $reference = $event->data->tx_ref;
             \MttsLms\Controllers\Student\PaymentController::verify_transaction( $reference );
             return rest_ensure_response( array( 'status' => 'success' ) );
        }

        return rest_ensure_response( array( 'status' => 'ignored' ) );
    }
}
