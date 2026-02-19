<?php
namespace MttsLms\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReceiptController {

    public static function init() {
        add_shortcode( 'mtts_payment_receipt', array( __CLASS__, 'render_receipt' ) );
    }

    public static function render_receipt( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'mtts_payment_receipt' );

        $transaction_id = intval( isset($_GET['id']) ? $_GET['id'] : $atts['id'] );
        
        if ( ! $transaction_id ) {
            return '<p>Invalid receipt ID.</p>';
        }

        $transaction = \MttsLms\Models\Transaction::find( $transaction_id );

        if ( ! $transaction ) {
            return '<p>Transaction not found.</p>';
        }

        // Access check: only owner or admin
        $current_user_id = get_current_user_id();
        $student = \MttsLms\Models\Student::find( $transaction->student_id );
        
        if ( ! $student || ( $student->user_id != $current_user_id && ! current_user_can( 'manage_options' ) ) ) {
            return '<p>Unauthorized access.</p>';
        }

        $student_user = get_user_by( 'id', $student->user_id );
        $session = \MttsLms\Models\Session::find( $transaction->session_id );

        ob_start();
        include MTTS_LMS_PATH . 'includes/Views/receipt.php';
        return ob_get_clean();
    }
}
