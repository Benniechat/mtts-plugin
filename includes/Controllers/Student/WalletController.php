<?php
namespace MttsLms\Controllers\Student;

use MttsLms\Models\Wallet;
use MttsLms\Models\Transaction;
use MttsLms\Models\Session;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WalletController {

    public static function init() {
        add_action( 'wp_ajax_mtts_verify_receiver', array( __CLASS__, 'ajax_verify_receiver' ) );
    }

    public static function process( $student ) {
        // Handle Funding Initiation
        if ( isset( $_POST['mtts_fund_wallet'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_fund_wallet' ) ) {
            self::initiate_funding( $student );
        }

        // Handle Transfer
        if ( isset( $_POST['mtts_transfer_funds'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'mtts_transfer_funds' ) ) {
            self::handle_transfer( $student );
        }

        $balance = Wallet::get_by_student( $student->id )->balance;
        $history = Wallet::get_history( $student->id );

        include MTTS_LMS_PATH . 'includes/Views/Student/wallet.php';
    }

    private static function handle_transfer( $student ) {
        $amount          = floatval( $_POST['amount'] );
        $receiver_matric = sanitize_text_field( $_POST['receiver_matric'] );
        $description     = sanitize_text_field( $_POST['description'] ?? '' );

        if ( $amount <= 0 ) {
            echo '<div class="mtts-alert mtts-alert-danger">Invalid amount.</div>';
            return;
        }

        $result = Wallet::transfer( $student->id, $receiver_matric, $amount, $description );

        if ( $result['success'] ) {
            echo '<div class="mtts-alert mtts-alert-success">' . esc_html( $result['message'] ) . '</div>';
        } else {
            echo '<div class="mtts-alert mtts-alert-danger">' . esc_html( $result['message'] ) . '</div>';
        }
    }

    public static function ajax_verify_receiver() {
        if ( ! current_user_can( 'mtts_student' ) ) {
            wp_send_json_error( array( 'message' => 'Unauthorized.' ) );
        }

        $matric = sanitize_text_field( $_POST['matric'] );
        if ( empty( $matric ) ) {
            wp_send_json_error( array( 'message' => 'Matric number is required.' ) );
        }

        $receiver = \MttsLms\Models\Student::get_by_matric( $matric );

        if ( $receiver ) {
            // Get user display name
            $user = get_userdata( $receiver->user_id );
            $name = $user ? $user->display_name : 'Unknown Student';
            wp_send_json_success( array( 'name' => $name ) );
        } else {
            wp_send_json_error( array( 'message' => 'Receiver not found.' ) );
        }
    }

    private static function initiate_funding( $student ) {
        $amount = floatval( $_POST['amount'] );
        $gateway = sanitize_text_field( $_POST['gateway'] ); // paystack, flutterwave
        $session = Session::get_active_session();

        if ( $amount <= 0 ) {
            echo '<div class="mtts-alert mtts-alert-danger">Invalid amount.</div>';
            return;
        }

        $reference = 'MTTS-WALL-' . strtoupper( uniqid() );

        $data = array(
            'student_id' => $student->id,
            'session_id' => $session ? $session->id : 0,
            'reference' => $reference,
            'amount' => $amount,
            'gateway' => $gateway,
            'status' => 'pending',
            'purpose' => 'wallet_funding',
        );

        $id = Transaction::create( $data );

        if ( $id ) {
            // Construct Callback URL
            $callback_url = add_query_arg( 'view', 'wallet', home_url('/') );
            
            // Redirect to Payment Controller Logic or handle here?
            // To keep things DRY, we should reuse the Payment Logic or just redirect to the gateway directly.
            // Since PaymentController usually handles the redirection, maybe we can direct to a specific action there?
            // For now, let's duplicate the redirection logic or call a helper.
            
            // Simplified Redirection (Simulation)
            wp_redirect( add_query_arg( 'reference', $reference, $callback_url ) );
            exit;
        }
    }
}
