<?php
namespace MttsLms\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentHandler {

    /**
     * Generate a checkout URL for the selected gateway.
     * 
     * @param array $data {
     *     @type string $email
     *     @type float  $amount
     *     @type string $reference
     *     @type string $callback_url
     *     @type string $purpose
     * }
     * @return string|WP_Error Checkout URL or Error
     */
    public static function get_checkout_url( $data ) {
        $gateway = get_option('mtts_active_payment_gateway', 'paystack');
        
        switch ( $gateway ) {
            case 'paystack':
                return self::get_paystack_url( $data );
            case 'flutterwave':
                return self::get_flutterwave_url( $data );
            default:
                return new \WP_Error( 'invalid_gateway', 'Selected payment gateway is not supported yet.' );
        }
    }

    private static function get_paystack_url( $data ) {
        $secret_key = get_option('mtts_paystack_secret_key');
        if ( ! $secret_key ) {
            return new \WP_Error( 'missing_api_key', 'Paystack API key is not configured.' );
        }

        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $data['email'],
            'amount' => intval( $data['amount'] * 100 ), // Kobo for Paystack
            'reference' => $data['reference'],
            'callback_url' => $data['callback_url'],
            'metadata' => [
                'purpose' => $data['purpose']
            ]
        ];

        $response = wp_remote_post( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode( $fields )
        ]);

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $result = json_decode( wp_remote_retrieve_body( $response ) );
        if ( $result && $result->status ) {
            return $result->data->authorization_url;
        }

        return new \WP_Error( 'gateway_error', $result->message ?? 'Error contacting Paystack.' );
    }

    private static function get_flutterwave_url( $data ) {
        $secret_key = get_option('mtts_flutterwave_secret_key');
        if ( ! $secret_key ) {
            return new \WP_Error( 'missing_api_key', 'Flutterwave API key is not configured.' );
        }

        $url = "https://api.flutterwave.com/v3/payments";
        $fields = [
            'tx_ref' => $data['reference'],
            'amount' => $data['amount'],
            'currency' => 'NGN',
            'redirect_url' => $data['callback_url'],
            'customer' => [
                'email' => $data['email']
            ],
            'customizations' => [
                'title' => 'MTTS Admission Payment',
                'description' => $data['purpose']
            ]
        ];

        $response = wp_remote_post( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $secret_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode( $fields )
        ]);

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $result = json_decode( wp_remote_retrieve_body( $response ) );
        if ( $result && $result->status === 'success' ) {
            return $result->data->link;
        }

        return new \WP_Error( 'gateway_error', $result->message ?? 'Error contacting Flutterwave.' );
    }
}
