<?php
namespace MttsLms\Controllers\Student;

use MttsLms\Models\Transaction;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentController {

    public static function process( $student ) {
        // Handle Payment Return/Callback
        if ( isset( $_GET['reference'] ) ) {
            $verified = self::verify_transaction( sanitize_text_field( $_GET['reference'] ) );
            if ( $verified ) {
                echo '<div class="mtts-alert mtts-alert-success">Payment verified successfully!</div>';
            }
        } elseif ( isset( $_GET['tx_ref'] ) ) { // Flutterwave
             $verified = self::verify_transaction( sanitize_text_field( $_GET['tx_ref'] ) );
             if ( $verified ) {
                echo '<div class="mtts-alert mtts-alert-success">Payment verified successfully!</div>';
            }
        }

        // Handle Payment Initiation
        if ( isset( $_POST['mtts_pay_now'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_initiate_payment' ) ) {
            self::initiate_payment( $student );
        }

        $transactions = Transaction::get_student_transactions( $student->id );
        include MTTS_LMS_PATH . 'includes/Views/Student/payments.php';
    }

    private static function initiate_payment( $student ) {
        $amount = floatval( $_POST['amount'] );
        $purpose = sanitize_text_field( $_POST['payment_purpose'] );
        $session = Session::get_active_session();

        $method = sanitize_text_field( $_POST['payment_method'] ); // paystack, flutterwave, wallet

        if ( ! $session ) {
            return;
        }

        // Handle Wallet Payment
        if ( $method == 'wallet' ) {
            $wallet = \MttsLms\Models\Wallet::get_by_student( $student->id );
            if ( $wallet->balance < $amount ) {
                 echo '<div class="mtts-alert mtts-alert-danger">Insufficient wallet balance.</div>';
                 return;
            }

            $reference = 'MTTS-WALPAY-' . strtoupper( uniqid() );
            
            // Create Transaction
            $id = Transaction::create( array(
                'student_id' => $student->id,
                'session_id' => $session->id,
                'reference' => $reference,
                'amount' => $amount,
                'gateway' => 'wallet',
                'status' => 'success', // Instant success
                'purpose' => $purpose,
                'paid_at' => current_time( 'mysql' )
            ));

            // Debit Wallet
            \MttsLms\Models\Wallet::debit( $student->id, $amount, "Payment for $purpose", $reference );
            
            echo '<div class="mtts-alert mtts-alert-success">Payment successful via Wallet!</div>';
            return;
        }

        $reference = 'MTTS-' . strtoupper( uniqid() );

        $data = array(
            'student_id' => $student->id,
            'session_id' => $session->id,
            'reference' => $reference,
            'amount' => $amount,
            'gateway' => $method, 
            'status' => 'pending',
            'purpose' => $purpose,
        );

        $id = Transaction::create( $data );

        if ( $id ) {
            // Construct Callback URL
            $callback_url = add_query_arg( 'view', 'payments', home_url('/') ); // Simplified
            
            // Redirect to Gateway (Placeholder logic)
            // In production, you would make an API call to 'initialize' endpoint to get the auth_url
            // $authorization_url = self::get_paystack_url($amount, $student->email, $reference, $callback_url);
            
            // For now, to test the loop, we redirect back to self with reference
            // This simulates the user returning from the gateway
            wp_redirect( add_query_arg( 'reference', $reference, $callback_url ) );
            exit;
        }
    }

    public static function verify_transaction( $reference ) {
        // Find transaction by reference
        global $wpdb;
        $table = $wpdb->prefix . 'mtts_transactions';
        $transaction = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE reference = %s", $reference ) );

        if ( ! $transaction ) return false;

        if ( $transaction->gateway == 'paystack' ) {
            return self::verify_paystack( $transaction );
        } elseif ( $transaction->gateway == 'flutterwave' ) {
            return self::verify_flutterwave( $transaction );
        }

        return false;
    }

    private static function verify_paystack( $transaction ) {
        $secret_key = get_option( 'mtts_paystack_secret_key' );
        if ( ! $secret_key ) return false;

        $response = wp_remote_get( "https://api.paystack.co/transaction/verify/" . $transaction->reference, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key
            )
        ) );

        if ( is_wp_error( $response ) ) return false;

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        if ( $data->status && $data->data->status == 'success' ) {
            // Update transaction
            \MttsLms\Models\Transaction::update( $transaction->id, array(
                'status' => 'success',
                'paid_at' => current_time( 'mysql' )
            ) );

            // Credit Wallet if purpose is wallet_funding
            if ( $transaction->purpose == 'wallet_funding' ) {
                \MttsLms\Models\Wallet::credit( 
                    $transaction->student_id, 
                    $transaction->amount, 
                    'Wallet Funding via Paystack', 
                    $transaction->reference 
                );
            }

            return true;
        }

        return false;
    }

    private static function verify_flutterwave( $transaction ) {
        $secret_key = get_option( 'mtts_flutterwave_secret_key' );
        if ( ! $secret_key ) return false;
        
        $response = wp_remote_get( "https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref=" . $transaction->reference, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $secret_key
            )
        ) );

        if ( is_wp_error( $response ) ) return false;

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        if ( $data->status == 'success' && $data->data->status == 'successful' && $data->data->amount >= $transaction->amount ) {
             \MttsLms\Models\Transaction::update( $transaction->id, array(
                'status' => 'success',
                'paid_at' => current_time( 'mysql' )
            ) );

            // Credit Wallet if purpose is wallet_funding
            if ( $transaction->purpose == 'wallet_funding' ) {
                \MttsLms\Models\Wallet::credit( 
                    $transaction->student_id, 
                    $transaction->amount, 
                    'Wallet Funding via Flutterwave', 
                    $transaction->reference 
                );
            }

            return true;
        }

        return false;
    }
}
